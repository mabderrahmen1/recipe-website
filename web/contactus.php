<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-Nous</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Contactez-Nous</h1>
            <nav>
                <a href="index.php">Accueil</a>
                <a href="about.html">À Propos</a>
                <a href="contact.html">Contact</a>
            </nav>
        </header>

        <main>
            <section>
                <h2>Nous Contacter</h2>
                <p>
                    Vous avez des questions, des suggestions ou des commentaires ? N'hésitez pas à nous contacter en remplissant le formulaire ci-dessous. Nous vous répondrons dans les plus brefs délais.
                </p>
            </section>

            <section>
                <form action="submit_contact.php" method="POST">
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
</body>
</html>