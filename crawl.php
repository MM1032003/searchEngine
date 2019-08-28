<?php 
    include 'config.php';
    include 'classes/DomDocumentParser.php';
    $alreadyCrawled     = array();
    $crawling           = array();
    $alreadyFoundImages = array();
    function linkExists($url) {
        global $dbcon;
        $stmt   = $dbcon->prepare("SELECT * FROM sites WHERE url = :url");
        $stmt->bindParam(":url", $url);
        $stmt->execute();
        $count  = $stmt->rowCount();
        return $count != 0;
    }
    function insertLink($url, $title, $description, $keywords) {
        global $dbcon;
        // Other Way
        /* 
            $stmt = $dbcon->prepare("INSERT INTO `sites` (`url`, `title`, `describtion`, `keywords`) VALUES (:url, :title, :description, :keywords)");
            $stmt->bindParam(":url", $url);
            .....
            */
        
        $stmt = $dbcon->prepare("INSERT INTO `sites` (`url`, `title`, `describtion`, `keywords`) VALUES (?, ?, ?, ?)");
        $stmt->execute(array($url, $title, $description, $keywords));
    }
    function insertImage($url, $src, $alt, $title) {
        global $dbcon;
        // Other Way
        
        $stmt = $dbcon->prepare("INSERT INTO `images` (`siteUrl`, `imageurl`, `alt`, `title`) VALUES (?, ?, ?, ?)");
        $stmt->execute(array($url, $src, $alt, $title));
    }
    function createLink($src, $url) {
        $scheme =   parse_url($url)['scheme']; // http
        $host   =   parse_url($url)['host'];    // www.bbc.com
        if (substr($src, 0, 2) == '//') {
            $src    = $scheme . ":" . $src;
        } else if (substr($src, 0, 1) == '/') {
            $src    = $scheme . "://" . $host . $src;
        } else if (substr($src, 0, 2) == './') { // ./about/about.php
            echo parse_url($url)['path'];
            $src    = $scheme . "://" . $host . dirname(parse_url($url)['path']) . substr($src, 1);
            //  http://www.bbc.com/.
        } else if (substr($src, 0, 3) == '../') { // ./about/about.php
            $src    = $scheme . "://" . $host . '/'. $src; // http://www.bbc.com/
        } else if (substr($src, 0, 5) != 'https' && substr($src, 0, 4) != 'http') {
            $src    = $scheme . "://" . $host . '/' . $src;

        } 
        return $src;
    }
    function getDetails($url) {
        global $alreadyFoundImages;
        $Parser         = new DomDocumentParser($url);
        $titleArray     = $Parser->getTitleTags();
        if (sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
            return;
        }
        $title          = $titleArray->item(0)->nodeValue;
        $title          = str_replace("\n", "", $title);
        if ($title == "") {
            return;
        }
        $describtion= "";
        $keywords   = "";
        $metaArray = $Parser->getMetaTags();
        foreach ($metaArray as $meta) {
            if ($meta->getAttribute('name') == 'description') {
                $describtion = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('name') == 'keywords') {
                $keywords = $meta->getAttribute('content');
            }
        }
        $describtion    = str_replace("\n", "", $describtion);
        $keywords       = str_replace("\n", "", $keywords);
        if (linkExists($url)) {
            echo "URL:[$url] Is Already Exists";
        } else if (insertLink($url, $title, $describtion, $keywords)) {
            echo "SUCCESS : $url";
        } else {
            echo "Failed To Insert This URL : [$url]";
        }
        $img = "";
        $imgArray = $Parser->getImgTags();
        foreach ($imgArray as $image) {
            $imgSrc = $image->getAttribute('src');
            $imgTitle = $image->getAttribute('title');
            $imgAlt = $image->getAttribute('alt');
            if (!$imgTitle && !$imgAlt) {
                continue;
            }
            $imgSrc = createLink($imgSrc, $url);
            if (!in_array($imgSrc, $alreadyFoundImages)) {
                $alreadyFoundImages[] = $imgSrc;
                insertImage($url, $imgSrc, $imgAlt, $imgTitle);
            }
        }
    }
    function followLinks($url) {
        global $crawling, $alreadyCrawled;
        $Parser = new DomDocumentParser($url);
        $links  = $Parser->getLinks();
        foreach ($links as $link) {
            $href   = $link->getAttribute("href"); // about/about.php
            if (strpos($href, '#') !== false) {
                continue;
            }
            if (substr($href, 0, 11) == 'javascript:') {
                continue;
            }
            $href = createLink($href, $url);
            if (!in_array($href, $alreadyCrawled)) {
                $alreadyCrawled[]   = $href;
                $crawling[]         = $href;
                getDetails($href);
            }

            // echo $href . '<br />';
        }
        array_shift($crawling);
        foreach ($crawling as $site) {
            followLinks($site);
        }
    }
    $startURL = 'https://www.noor-book.com/';
    followLinks($startURL);
?>
