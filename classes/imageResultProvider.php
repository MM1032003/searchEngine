<?php
class imageResultsProvider {
    private $con;
    public function __construct($con) {
        $this->con = $con;
    }
    public function getNumResult($q) {
        $stmt = $this->con->prepare("SELECT 
                                        COUNT(*) as total FROM images
                                    WHERE
                                        (`alt` LIKE :term
                                    OR
                                        `title` LIKE :term)
                                    AND
                                        `broken` = 0");
        $qSearch = "%" . $q . "%";
        $stmt->bindParam(':term', $qSearch);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }   

    public function getResultHtml($page, $pageSize, $q) {
        $fromLimit = ($page - 1) * $pageSize;
        $stmt = $this->con->prepare("SELECT 
                                        * FROM images
                                    WHERE
                                        (`alt` LIKE :term
                                    OR
                                        `title` LIKE :term)
                                    AND
                                        `broken` = 0
                                    ORDER BY `clicks` DESC
                                    LIMIT :fromLimit, :pagesize");
        $qSearch = "%" . $q . "%";
        $stmt->bindParam(':term', $qSearch);
        $stmt->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
        $stmt->bindParam(":pagesize", $pageSize, PDO::PARAM_INT);
        $stmt->execute();

        $resultHtml = "<div class='imageResults'>";
        $count = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count++;
            $id         = $row['id'];
            $SiteUrl    = $row['siteUrl'];
            $ImageUrl   = $row['imageurl'];
            $alt        = $row['alt'];
            $title      = $row['title'];
            if ($title) {
                $displayText = $title;
            } else if ($alt) {
                $displayText = $alt;
            } else {
                $displayText = $ImageUrl;
            }
            // Insert To ResultHtml
            $resultHtml     .=  "<div class='girdItem image$count'>
                                    <a href='$ImageUrl' class='fancy' data-fancybox=\"images\"  data-caption='$displayText' data-url='$SiteUrl'>
                                        <script>
                                            document.addEventListener(\"DOMContentLoaded\", function(event) {
                                                loadImage(\"$ImageUrl\", \"image$count\", $id);
                                            });
                                        </script>
                                        <span class='details'>$displayText</span>
                                    </a>
                                </div>";
        }
        $resultHtml .= "</div>";
        return $resultHtml;
    }
}
?>