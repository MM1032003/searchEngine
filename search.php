<?php
    $q      = isset($_GET['q']) ? $_GET['q'] : exit('Enter What You Want To Search');
    $type   = isset($_GET['type']) ? $_GET['type'] : 'sites';
    $page   = isset($_GET['page']) ? $_GET['page'] : 1;
    include "config.php";
    include "classes/siteResultsProvider.php";
    include "classes/imageResultProvider.php";
    if ($page == 0) {
        exit ("Page 0 ?!!!");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Search Engine</title>
    <link rel="stylesheet" href="assests/css/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
</head>
<body>
    <div class="wrapper">

        <div class="header">

            <div class="headerContent">

                <div class="logoContainer">

                    <a href='index.php'>

                        <img src='assests/img/hamo.png' alt='Search Image' />

                    </a>
                </div>

                <div class="searchContainer">

                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                        <div class="searchContainerBar">
                            <input type="hidden" name="type" value="<?php echo $type; ?>">
                            <input type="search" class='searchBox' name='q' value='<?php if(isset($q)) {echo $q;} ?>' id="searchBox"/>
                            <button class='searchButton'>
                                <img src="assests/img/icons/icon.png">
                            </button>
                        </div>
                    </form>


                </div>

            </div>

            <div class="tapsContent">
                <ul class='tabList'>
                    <li style='<?php if ($type == 'sites') {echo 'border-bottom:blue solid 2px;font-weight:bold;';} ?>' >
                        <a style='<?php if ($type == 'sites') {echo 'color:blue;';} ?>' href="<?php echo "search.php?q=$q&type=sites"; ?>">Sites</a>
                    </li>
                    <li style='<?php if ($type == 'images') {echo 'border-bottom:blue solid 2px;font-weight:bold;';} ?>'>
                        <a style='<?php if ($type == 'images') {echo 'color:blue;';} ?>' href='<?php echo "search.php?q=$q&type=images"; ?>'>Images</a>
                    </li>

                </ul>
            </div>

        </div>
        <div class="main-resultSection">
            <?php 
                if ($type == 'sites') {
                    $results    = new siteResultsProvider($dbcon);
                    $pageLimit  = 20;
                } else if ($type == 'images') {
                    $results    = new imageResultsProvider($dbcon);
                    $pageLimit  = 30;
                }
                $num = $results->getNumResult($q);
                echo '<p class=\'num-result\'>' . $num . ' Result Found</p>';
                echo $results->getResultHtml($page, $pageLimit, $q);
            ?>
        </div>
        <div class="paginationContainer">
            <div class="pageButtons">
                <div class="pageNumButton">
                    <img src="assests/img/icons/H_icon.png" alt="page-Start">
                </div>
                <?php 
                    $pageToShow     = 10;
                    $numPages       = ceil($num / $pageLimit); // 14
                    $pageLeft       = min($pageToShow, $numPages); // 10
                    $currentPage    = $page - floor(($pageToShow / 2)); // 28 - 5 = 22//  /0 /5 /10 / 10
                    if ($currentPage < 1) {
                        $currentPage = 1;
                    }

                    if ($currentPage + $pageLeft > $numPages + 1) {
                        $currentPage = ($numPages + 1) - $pageLeft;
                    }

                    while ($pageLeft != 0 && $currentPage <= $numPages) {
                        if ($currentPage == $page) {
                            echo "<div class='pageNumButton'>";
                                    echo "<img src='assests/img/icons/pageSelected.png' />";
                                    echo "<span class='pageNumber'>$currentPage</span>";
                            echo "</div>";
                        } else {
                            echo "<div class='pageNumButton'>";
                                echo "<a href='" . "?q=$q&type=$type&page=$currentPage" . "'>";
                                    echo "<img src='assests/img/icons/page.png' />";
                                    echo "<span class='pageNumber'>$currentPage</span>";
                                echo "</a>";
                            echo "</div>";                           
                        }
                        $currentPage++;
                        $pageLeft--;
                    }
                ?>
                <div class="pageNumButton">
                    <img src="assests/img/icons/MO_icon.png" alt="page-Start">
                </div>
            </div>
        </div>

    </div>
    <script type='text/javascript' src='http://code.jquery.com/jquery-latest.js'></script>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script src='assests/js/script.js'></script>
</body>
</html>
<?php 

?>