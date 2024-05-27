<?php
session_start();

include("includes/functions.php");

// Si c'est la première visite de la session, enregistre l'heure de début
if (!isset($_SESSION['start_time'])) {
  $_SESSION['start_time'] = time();
  recordPageVisit('index');
} else {
  // Si la session a déjà commencé, vérifie si la durée de visite doit être mise à jour
  updateDuration('index');
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Boutique Kink & BDSM - En Construction</title>
  <link rel="stylesheet" href="css/index.css">
  <link rel="icon" href="img/logo.png">
</head>

<body>
  <?php include("includes/header.php"); ?>
  <section class="hero">

    <div class="hero-text">
      <h1>Tout un univers kink et BDSM arrive bientôt!</h1>
      <p>Nous travaillons en se moment pour vous faire découvrez une collection exclusive de harnais, menottes, colliers, et laisses, commandes sur mesure, tous faits main.</p>
      <div class="construction-animation">
        <!-- Ajoutez ici l'animation de la pelleteuse -->
        <iframe src="anim/pelleteuse.html" frameborder="0"></iframe>
      </div>
      <form action="click.php?platform=newsletter&from=index" method="post" class="newsletter-form">
        <p style="margin-bottom:5px;">Inscrivez vous a la NewsLetter pour rester au courant !</p>
        <input type="email" name="email" placeholder="Entrez votre email" required>
        <button type="submit">S'inscrire</button>
        <?php
        if (isset($_SESSION['registered']) && $_SESSION['registered'] == true) {

          echo '<div class="confirmation-message">Inscription réussie !</div>';
          echo '<script>
            setTimeout(function() {
              var confirmationMessage = document.querySelector(".confirmation-message");
              confirmationMessage.style.display = "none";
            }, 10000);
          </script>';
          $_SESSION['registered'] = false;
        }
        ?>
      </form>
    </div>
  </section>
  <section class="products-preview">
    <h2>Aperçu de nos créations</h2>

    <div class="product-gallery">
      <div class="product-item">
        <img src="img/produits/produit-1.jpeg" alt="Produit 1">
        <p>Harnais en cuir</p>
      </div>
      <div class="product-item">
        <img src="img/produits/produit-2.jpeg" alt="Produit 2">
        <p>Harnais néoprenne</p>
      </div>
      <div class="product-item">
        <img src="img/produits/produit-3.jpeg" alt="Produit 3">
        <p>Harnais tissu</p>
      </div>
      <div class="product-item">
        <img src="img/produits/produit-4.jpeg" alt="Produit 4">
        <p>Collier neoprenne</p>
      </div>
      <div class="product-item">
        <img src="img/produits/produit-5.jpeg" alt="Produit 4">
        <p>Menotte cuir</p>
      </div>
      <div class="product-item">
        <img src="img/produits/produit-6.jpeg" alt="Produit 4">
        <p>Laisses robustes</p>
      </div>

    </div>

  </section>
  <section class="countdown">
    <h2>Lancement dans...</h2>
    <div id="countdown-timer">30 Jours</div>
  </section>
  <section class="social-media">
    <h2>Suivez-nous</h2>
    <div class="social-icons">
      <a href="click.php?redirect=insta-pro&from=index"><img src="img/logo-insta.png" alt="Instagram" class="logo">Instagram</a>
      <a href="click.php?redirect=x-pro&from=index"><img src="img/logo-x.png" alt="Twitter" class="logo">Twitter/X</a>
    </div>
  </section>
  <footer>
    <div class="contact-info">
      <p>Email: <a href="mailto:ushthepup@gmail.com">ushthepup@gmail.com</a></p>
    </div>
    <nav>
      <ul>
        <li><a href="click.php?redirect=accueil&from=index">Accueil</a></li>
        <li><a href="click.php?redirect=faq&from=index">À propos</a></li>
        <li><a href="click.php?redirect=newsletter&from=index">Newsletter</a></li>
        <li><a href="click.php?redirect=contact&from=index">Contact</a></li>
      </ul>
    </nav>
    <p>&copy; 2024 UshThePup. Tous droits réservés.</p>
  </footer>
  <script>
    // Ajoutez cette fonction pour le déplacement des produits

    document.addEventListener('DOMContentLoaded', function() {
      const gallery = document.querySelector('.product-gallery');
      const items = document.querySelectorAll('.product-item');
      const itemWidth = items[0].offsetWidth + parseInt(getComputedStyle(items[0]).marginRight);

      // Clone items for infinite scrolling
      for (let i = 0; i < 3; i++) {
        items.forEach(item => {
          const clone = item.cloneNode(true);
          gallery.appendChild(clone);
        });
      }
      let currentIndex = 0;

      function updateGallery() {
        const offset = -currentIndex * itemWidth;
        gallery.style.transform = `translateX(${offset}px)`;
      }

      // Auto-scroll functionality
      let autoScrollInterval = setInterval(() => {
        currentIndex = (currentIndex >= items.length - 1) ? 0 : currentIndex + 1;
        updateGallery();
      }, 2000);

      // Pause auto-scroll on hover
      gallery.addEventListener('mouseover', () => {
        clearInterval(autoScrollInterval);
      });

      gallery.addEventListener('mouseout', () => {
        autoScrollInterval = setInterval(() => {
          currentIndex = (currentIndex >= items.length - 1) ? 0 : currentIndex + 1;
          updateGallery();
        }, 2000);
      });
    });
  </script>
</body>

</html>