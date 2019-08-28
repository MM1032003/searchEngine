<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name='describtion' content="Hello This Is Mohamed's Search Web Site" />
    <meta name='author' content="Mohamed Ali" />
    <meta name='keyword' content="google, programming, search engine, Hamo" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Search Engine</title>
    <link rel="stylesheet" href="assests/css/style.css" />
</head>
<body>
    <div class="wrapper indexPage">

        <div class="mainSection">

            <div class="logoContainer">

                <img src='assests/img/hamo.png' alt='Search Image' title="Hamo Logo"/>

            </div>
            <div class="searchContainer">

                <form action="search.php" method='GET'>

                    <input class='SearchBox' type="text" name="q" id="search"/>
                    <input class='SerachButton' type="submit" id="SerachButton" value='Search'/>

                </form>
            </div>

        </div>

    </div>

</body>
</html>