<?php

/**
 * @author Mateusz Rąbalski
 * Whis is a file with functions used in script
 */
abstract class Functions 
{
    public function create_doc($CreateDOM)
    {
        $doc = new DOMDocument();
        $doc->load($CreateDOM->_rss_link);
        
        return $doc;
    }
    
    public function create_log()
    {
        $now = date("Y-m-d H:i:s")."  Rozpoczeto wykonywanie skryptu\n";

        $file = "log.txt";

        $fp = fopen($file, "a");

        flock($fp, 2);

        fwrite($fp, $now );

        flock($fp, 3);

        fclose($fp);        
    }
    
    public function create_log_with_article_id($title, $mysql_connect)
    {
        $sql = "SELECT id FROM rss_links WHERE title='$title'";
            
        $result = @$mysql_connect->query($sql);
 
        $article_id = mysqli_fetch_array($result);

        $now  = date("Y-m-d H:i:s")."  Dodano artykul o id: ".$article_id['id']." \n";

        $file = "log.txt";

        $fp = fopen($file, "a");

        flock($fp, 2);   
        fwrite($fp, $now );

        flock($fp, 3);

        fclose($fp);        
    }
    
    public function create_end_log()
    {
        $now  = date("Y-m-d H:i:s")."  Zakonczono wykonywanie skryptu\n\n";

        $file = "log.txt";

        $fp = fopen($file, "a");

        flock($fp, 2);

        fwrite($fp, $now );

        flock($fp, 3);

        fclose($fp);        
    }
    
    public function check_data($title, $mysql_connect)
    {
        $sql = "SELECT * FROM rss_links WHERE title='$title'";
		
	if($result = @$mysql_connect->query($sql))
	{
            $articles_count = @$result->num_rows;
                        
            if($articles_count>0)
            {
                return 1;          
            }
            else
            {
                return 0;
            }
			
	}
    }
    
    public function count_articles($Object, $mysql_connect)
    {
        $sql = "SELECT * FROM rss_links WHERE rss_link='$Object->_rss_link'";
		
	if($result = @$mysql_connect->query($sql))
	{
            $articles_count = @$result->num_rows;
                        
            if($articles_count>4)
            {
                return 1;          
            }
            else
            {
                return 0;
            }			
	}
    }
    
    public function print_data($title, $mysql_connect)
    {
        $sql = "SELECT * FROM rss_links WHERE title='$title'";
            
        $result = @$mysql_connect->query($sql);
 
        $article_id = mysqli_fetch_array($result);

        echo $article_id['title'];
        echo '<br /><br />';
        echo $article_id['description'];
        echo '<br /><br />';
        echo $article_id['date'];
        
        echo '<br /><br />';
        echo'[<a href="log.txt">Przejrzyj log!</a>]</p>';
    }
    
    public function add_data_to_db($title, $description, $date, $link)
    {      
        $sql = "INSERT INTO rss_links (title,description,date,rss_link) VALUES ('$title','$description','$date','$link')";
        
        return $sql;
    }
}