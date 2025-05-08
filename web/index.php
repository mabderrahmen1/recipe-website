<!DOCTYPE html>
<html>

<head>
    <title>MyCuisine</title>
    <link rel="stylesheet" href="welcom.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function toto() {
            console.log("cbb");
            $("h2, h4, h5, h3, .images, ul").hide();

            $('#ContactContent').show();

            $.ajax({
                method: 'GET',
                url: 'contact.php',
                dataType: 'html',
                success: function(response) {
                    $('#ContactContent').html(response);
                }
            });
        }
    </script>
    <script>
        function toto1() {
            $("h2, h4, h5, h3, .images, ul").hide();

            $('#aboutusContent').show();

            $.ajax({
                method: 'GET',
                url: 'aboutus.html',
                dataType: 'html',
                success: function(response) {
                    $('#aboutusContent').html(response);
                }
            });
        }
    </script>
    <script>
        function toto2() {
            $("h1, h3, h4, h5, .images, .barre-2, ul").hide();
            $('#loginContent').show();

            $.ajax({
                method: 'GET',
                url: 'login.php',
                dataType: 'html',
                success: function(response) {
                    $('#loginContent').html(response);
                }
            });
        }
    </script>
</head>

<body>
    <div class="onglet" id="onglet2"></div>
    <div class="onglet" id="onglet1">
        <h1>MyCuisine</h1>
        <ul>
            <li><button id="contactButton" onclick='toto()'>Contact</button></li>
            <li><button id="aboutusButton" onclick='toto1()'>About</button></li>
            <li><a href="login.php">login</a></li>

        </ul>

        <div class="barre-2"> </div>
        <h3>Rejoins-nous sans tarder pour découvrir nos recettes incontournables !</h3>
        <h4>Des idées d'entrées </h4>
        <div class="images">
            <img src="entrée-1.jpg" alt="Image 1" style="width:256px;height:256px;">
            <img src="entrée-4.jpg" alt="Image 2" style="width:256px;height:256px;">
            <img src="entrée-3.jfif" alt="Image 3" style="width:256px;height:256px;">
        </div>
        <h5>Plutot cuisine Italienne ?</h5>
        <div class="images">
            <img src="entrée-5.jpg" alt="Image 1" style="width:256px;height:256px;">
            <img src="entrée-6.avif" alt="Image 2" style="width:256px;height:256px;">
            <img src="entrée-7.jpg" alt="Image 3" style="width:256px;height:256px;">
        </div>
    </div>
    <div id="loginContent" style="display: none;"></div>

    <div id="ContactContent" style="display: none;"></div>

    <div id="aboutusContent" style="display: none;"></div>

</body>

</html>