<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-Nous</title>
    <link rel="stylesheet" href="">
</head>

<body>
    <div class="container">
        <header>

        </header>

        <main>
            <section>
                <h2>Nous Contacter</h2>
                <p>
                    Vous avez des questions, des suggestions ou des commentaires ? N'hésitez pas à nous contacter en
                    remplissant le formulaire ci-dessous. Nous vous répondrons dans les plus brefs délais.
                </p>
            </section>

            <section>
                <form action="contact.php" method="POST">
                    <label for="name">Nom :</label>
                    <input type="text" id="name" name="name" required>

                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>

                    <label for="subject">Sujet :</label>
                    <input type="text" id="subject" name="subject" required>

                    <label for="message">Message :</label>
                    <textarea id="message" name="message" rows="5" required></textarea>

                    <button type="submit">Envoyer</button>
                </form>
            </section>
        </main>
    </div>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $sujet = $_POST["sujet"];
        $message = $_POST["message"];

        ajouterCommentaire($name, $email, $sujet, $message);
        exit();
    }


    function ajouterCommentaire($name, $email, $sujet, $message)
    {
        $jsonString = file_get_contents("support.json");
        $utilisateurs = json_decode($jsonString, true);

        if (!is_array($utilisateurs)) {
            $utilisateurs = [];
        }

        $nouveauCommentaire = [
            "name" => $name,
            "email" => $email,
            "sujet" => $sujet,
            "mdp" => $message,
        ];

        $Commentaires[] = $nouveauCommentaire;

        $newJsonString = json_encode($Commentaires, JSON_PRETTY_PRINT);

        file_put_contents("support.json", $newJsonString);
    }
    ?>
    <ul>
        <li><button id="Homebutton">Home</button></li>
    </ul>
    <div id="HomeContent" style="display: none;"></div>
    <script>
        $(document).ready(function() {
            $('#Homebutton').on('click', function() {
                $("h1, h2, form, ul, .forme, .Gender, .cnx").hide();

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
        });
    </script>


</body>

</html>