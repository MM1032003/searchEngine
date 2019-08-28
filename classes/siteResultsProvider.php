<?php
class siteResultsProvider {
    private $con;
    public function __construct($con) {
        $this->con = $con;
    }
    public function getNumResult($q) {
        $stmt = $this->con->prepare("SELECT 
                                        COUNT(*) as total FROM sites
                                    WHERE
                                        `url` LIKE :term
                                    OR
                                        `title` LIKE :term
                                    OR
                                        `keywords` LIKE :term
                                    OR
                                        `describtion` LIKE :term");
        $qSearch = "%" . $q . "%";
        $stmt->bindParam(':term', $qSearch);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }   

    public function getResultHtml($page, $pageSize, $q) {
        $fromLimit = ($page - 1) * $pageSize;
        $stmt = $this->con->prepare("SELECT 
                                        * FROM sites
                                    WHERE
                                        `url` LIKE :term
                                    OR
                                        `keywords` LIKE :term
                                    OR
                                        `title` LIKE :term
                                    OR
                                        `describtion` LIKE :term
                                    ORDER BY `clicks` DESC
                                    LIMIT :fromLimit, :pagesize");
        $qSearch = "%" . $q . "%";
        $stmt->bindParam(':term', $qSearch);
        $stmt->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $stmt->bindParam(":pagesize", $pageSize, PDO::PARAM_INT);
        $stmt->execute();

        $resultHtml = "<div class='siteResults'>";
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id             = $row['id'];
            $url            = $row['url'];
            $title          = $row['title'];
            $description    = $row['describtion'];
            // Insert To ResultHtml
            $title          = str_replace("�", "", $this->trimField($title, 55));
            $description    = str_replace("�", "", $this->trimField($description, 156));
            $resultHtml     .=  "<div class='resultContainer'>
                                    <h3 class='title'>
                                        <a id='resultUrl' href='$url' data-linkId='" . $row['id'] . "'>
                                            $title
                                        </a>
                                    </h3>
                                    <span class='url'>$url</span> 
                                    <span class='describtion'>$description</span> 

                            </div>";
        }
        $resultHtml .= "</div>";
        return $resultHtml;
    }
    private function trimField($string, $charLimit) {
        $dots = strlen($string) > $charLimit ? "...." : "";
        return substr($string, 0, $charLimit) . $dots;
    }
}
?>