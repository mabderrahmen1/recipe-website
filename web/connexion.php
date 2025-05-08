<?php
$jsonFile = 'LOGIN.json';
$data = json_decode(file_get_contents($jsonFile), true);

if (isset($_POST["email"]) && isset($_POST["mdp"])) {
    $emailID = $_POST["email"];
    $mdpID = $_POST["mdp"];
    $userFound = false;

    foreach ($data as $key => $user) {
        if ($user['email'] === $emailID && $user['mdp'] === $mdpID) {
            $data[$key]['actif'] = true;
            $userFound = true;
        } else {
            $user['actif'] = false;
        }
    }
    

    if ($userFound) {
        file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
        header('Location: site.php');
        exit();
    } else {
        echo "<p style='color: red;'>Erreur : Email ou mot de passe incorrect.</p>";
    }
}

function verfieConnexion($emailID, $mdpID)
{
    $jsonString = file_get_contents('LOGIN.json');
    $data  = json_decode($jsonString, true);
    foreach ($data as $id) {
        if ($id['email'] === $emailID && $id['mdp'] === $mdpID) {
            return true;
        }
    }
    return false;
}


?>
<!DOCTYPE html>
<html>

<head>
    <title>connexion</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>


<body>
    <h1>Log in</h1>
    <form action="connexion.php" method="post" class="forme">
        <div class="forme">
            <label for="email">E-mail: </label>
            <input type="email" name="email" id="email" required />
        </div>
        <div class="forme">
            <label for="mdp">Mot de passe: </label>
            <input type="password" name="mdp" id="mdp" required />
        </div>
        <input type="submit" class="submitButton" value="Se connecter" />    </form>

    
    <div class="cnx">
        <a href="login.php" type="button "class="retourButton">Crée un compte</a>
    </div>
    <div class="cnx">
        <a href="index.php" type="button "class="retourButton">Acceil</a>
    </div>
    <!--

<script>
    $(document).ready(function() {
        $('#Homebutton').on('click', function() {
            $("h1, form, ul, .forme").hide();
            $('#HomeContent').show();
            $.ajax({
                method: 'GET',
                url: 'welcom.php',
                dataType: 'html',
                success: function(response) {
                    $('#HomeContent').html(response);
                }
            });
        });

        $('#connectButton').on('click', function() {
            let email = $("#email").val();
            let mdp = $("#mdp").val();

            if (!email || !mdp) {
                $("#message").html("<span style='color: red;'>Tous les champs sont obligatoires.</span>");
                return;
            }

            $.ajax({
                method: "POST",
                url: "connexion.php",
                data: {
                    email: email,
                    mdp: mdp
                },
                success: function(response) {
                    if (response == "success") {
                        window.location.href = "site.php";
                    } else {
                        $("#message").html("<span style='color: red;'>" + response + "</span>");
                    }
                },
                error: function() {
                    $("#message").html("<span style='color: red;'>Erreur lors de la connexion.</span>");
                }
            });
        });
    });
</script>
-->

</body>


</html>