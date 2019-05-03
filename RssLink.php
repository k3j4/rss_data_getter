<?php

/**
 *  @author Mateusz Rąbalski
 *  In this class we get data from links and save them in db
 */
require_once "Functions.php";
require_once "DatabaseAccess.php";

session_start();

class RssLink extends Functions
{
    private $_number_of_article;
    
    protected $_rss_link; // Link getted from form
    
    private $_xml_link_name; // XML code address
    private $_rss_link_name; // RSS code address

    public function __construct()
    {
        $this->_number_of_article = 1;

        $this->_rss_link = $_SESSION['link'];
        
        $this->_xml_link_name = 'http://xmoon.pl/rss/rss.xml';
        $this->_rss_link_name = 'http://www.rmf24.pl/sport/feed'; 
    } 
    
    public function get_data_from_rss($number_of_article, $doc, $RssLink, $mysql_connect)
    {
        $i=0;

        foreach ($doc->getElementsByTagName('item') as $node) 
        {
            if($i >= $number_of_article)
            {
		break;
            }
        
            $i++;

            $itemRSS = array ( 
            'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
            'description' => $node->getElementsByTagName('description')->item(0)->nodeValue,
            'pubDate' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
            );
            
            $title = $itemRSS['title'];
            $description = $itemRSS['description'];
            $pubDate = $itemRSS['pubDate'];      
            
            $count_articles = $RssLink->count_articles($RssLink, $mysql_connect);
            $check_article = $RssLink->check_data($title, $mysql_connect);

            if($count_articles == 0)
            {
                if($check_article == 0)
                {
                    $sql = $RssLink->add_data_to_db($title, $description, $pubDate, $RssLink->_rss_link);
                    
                    if($result = @$mysql_connect->query($sql))
                    {			
                        echo "dodano artykuł do bazy danych";
                        echo '<br /><br />';
                        $RssLink->create_log_with_article_id($title, $mysql_connect);
                        $RssLink->print_data($title, $mysql_connect);
                    }
                    else
                    {
                        echo "nie dodano artykułu do bazy danych";
                    }
                }
                else
                {
                    $number_of_article++;
                }               
            } 
            else
            {
                echo "W bazie danych jest już 5 rekordów z tego linku";
            }
        }       
    }
    
    public function get_data_from_xml($number_of_article, $doc, $XmlLink, $mysql_connect)
    {
        $i=0;
        
        foreach ($doc->getElementsByTagName('entry') as $node) 
        {
            if($i>=$number_of_article)
            {
		break;
            }
        
            $i++;

            $itemRSS = array ( 
            'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
            'content' => $node->getElementsByTagName('content')->item(0)->nodeValue,
            'published' => $node->getElementsByTagName('published')->item(0)->nodeValue,
            );

            $title = $itemRSS['title'];
            $content = $itemRSS['content'];
            $published = $itemRSS['published'];
            
            $count_articles = $XmlLink->count_articles($XmlLink, $mysql_connect);
            $check_article = $XmlLink->check_data($title, $mysql_connect);

            if($count_articles==0)
            {
                if($check_article==0)
                {
                    $sql = $XmlLink->add_data_to_db($title, $content, $published, $XmlLink->_rss_link);
                    
                    if($result = @$mysql_connect->query($sql))
                    {	
                        $XmlLink->create_log_with_article_id($title, $mysql_connect);
                        
                        echo "dodano artykuł do bazy danych";
                        echo '<br /><br />';
                        
                        $XmlLink->print_data($title, $mysql_connect);
                    }
                    else
                    {
                        echo "nie dodano artykułu do bazy danych";
                    }
                }
                else
                {
                    $number_of_article++;
                } 
            }
            else
            {
                echo "W bazie danych jest już 5 rekordów z tego linku";
            }
        }
    }
    
    public function run_script($RssLink, $mysql_connect)
    {         
        $doc = $RssLink->create_doc($RssLink);
        
        if($RssLink->_rss_link == $RssLink->_rss_link_name)
        {
            $RssLink->get_data_from_rss($RssLink->_number_of_article, $doc, $RssLink, $mysql_connect);
        }
        else
        {
            $RssLink->get_data_from_xml($RssLink->_number_of_article, $doc, $RssLink, $mysql_connect);
        }
        
    }
}
// ------------Start script--------------- //

$mysql_connect = @new mysqli($host,$db_user,$db_password,$db_name);
	
if($mysql_connect->connect_errno!=0)
{
    echo "Error:".$mysql_connect->connect_errno."  Description:".$mysql_connect->connect_error;
}
else
{
    $rss_link = new RssLink();
    
    $rss_link->run_script($rss_link, $mysql_connect);
    $rss_link->create_end_log();
}

$mysql_connect->close();
 
    

