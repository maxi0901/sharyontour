<?php
$pageTitle = 'Impressum';
$siteConfig = require __DIR__ . '/includes/site-config.php';
$eventMedia = $siteConfig['event_media'];
require __DIR__ . '/includes/header.php';
?>
<section class="legal-page imprint-page">
  <div class="legal-content-wrapper legal-content">
      <h1>Impressum</h1>

      <div class="legal-section">
        <h2>Angaben gemäß § 5 TMG</h2>
        <p>
          Galerie S-ART<br>
          Lindenallee 10<br>
          45127 Essen / Germany
        </p>
        <p>
          Vertreten durch:<br>
          Sharyar Azhdari
        </p>
      </div>

      <div class="legal-section">
        <h2>Kontakt</h2>
        <p>
          Mobil: <a href="tel:<?= e($siteConfig['contact']['phone_href']) ?>"><?= e($siteConfig['contact']['phone_display']) ?></a><br>
          E-Mail: <a href="mailto:info@sart.work">info@sart.work</a>
        </p>
      </div>

      <div class="legal-section">
        <h2><?= e($eventMedia['department']) ?></h2>
        <p><?= e($eventMedia['management']) ?></p>
        <?php foreach ($eventMedia['contacts'] as $contact): ?>
          <p>
            <strong><?= e($contact['name']) ?></strong><br>
            <?php if (!empty($contact['phone_display']) && !empty($contact['phone_href'])): ?>
              Telefon: <a href="tel:<?= e($contact['phone_href']) ?>"><?= e($contact['phone_display']) ?></a><br>
            <?php endif; ?>
            E-Mail: <a href="mailto:<?= e($contact['email']) ?>"><?= e($contact['email']) ?></a>
          </p>
        <?php endforeach; ?>
      </div>

      <div class="legal-section">
        <h2>Webseite und Technik</h2>
        <p>
          Dodidis Media<br>
          Inhaber: Raphael Dodidis<br>
          Meysenbugstraße 6<br>
          34119 Kassel
        </p>
        <p>E-Mail: <a href="mailto:kontakt@dodidis-media.de">kontakt@dodidis-media.de</a></p>
      </div>


      <div class="legal-section">
        <h2>Umsatzsteuer-ID</h2>
        <p>
          Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:<br>
          DE458587908
        </p>
      </div>

      <div class="legal-section">
        <h2>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
        <p>
          Raphael Dodidis<br>
          Meysenbugstraße 6<br>
          34119 Kassel
        </p>
      </div>

      <div class="legal-section">
        <h2>EU-Streitschlichtung</h2>
        <p>
          Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit:
          <a href="https://ec.europa.eu/consumers/odr/" target="_blank" rel="noopener noreferrer">https://ec.europa.eu/consumers/odr/</a>.<br>
          Unsere E-Mail-Adresse finden Sie oben im Impressum.
        </p>
      </div>

      <div class="legal-section">
        <h2>Verbraucherstreitbeilegung/Universalschlichtungsstelle</h2>
        <p>
          Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.
        </p>
      </div>

      <div class="legal-section">
        <h2>Haftung für Inhalte</h2>
        <p>
          Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich.<br>
          Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen.
        </p>
      </div>

      <div class="legal-section">
        <h2>Haftung für Links</h2>
        <p>
          Unsere Website enthält ggf. Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben.<br>
          Für diese fremden Inhalte übernehmen wir keine Gewähr.
        </p>
      </div>

      <div class="legal-section">
        <h2>Urheberrecht</h2>
        <p>
          Die durch den Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht.<br>
          Die Vervielfältigung, Bearbeitung und Verbreitung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung.
        </p>
      </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
