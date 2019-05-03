<?php
/**
 * @author Mateusz Rąbalski
 * This class checks the correctness of the link
 */
session_start();

require_once "Functions.php";

class RecognizeLink extends Functions
{
    private $_rss_link; // variable with link
    
    public function __construct()
    {
        $this->_rss_link = $_POST['Rss_link'];
        $this->_rss_link  = htmlentities($this->_rss_link,ENT_QUOTES,"UTF-8");
        
        $_SESSION['link'] = $this->_rss_link;
    } 
    
    public function check_link($RecognizeLink)
    {
        $url = @file_get_contents($RecognizeLink->_rss_link);
        
        if(!$url) 
        {
            echo('Strona nie odpowiada ');
            echo '<br /><br />';
        }
        else
        {
            header("Location: RssLink.php");
        }
    }               
    

    public function run_script($RecognizeLink)
    {
        $RecognizeLink->create_log();
        $RecognizeLink->check_link($RecognizeLink);
    }
}

$recognize_link = new RecognizeLink();

$recognize_link->run_script($recognize_link);