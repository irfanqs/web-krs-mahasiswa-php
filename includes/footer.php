<?php
/**
 * SIAKAD Gallery — Footer Include
 * Outputs the footer
 * Usage: <?php require_once 'includes/footer.php'; ?>
 */
?>
  <!-- Footer -->
  <footer class="page-footer">
    <div class="footer-links">
      <a href="#">Bantuan</a> &middot;
      <a href="#">Kebijakan Privasi</a> &middot;
      <a href="#">Syarat Penggunaan</a>
    </div>
    <p>SIAKAD Gallery © 2026 · Sistem Informasi Akademik Terpadu</p>
  </footer>

  <!-- Global Scripts -->
  <script>
    window.APP_URL = "<?= APP_URL ?>";
  </script>
  <script src="<?= APP_URL ?>/assets/js/app.js"></script>

  <style>
    .page-footer {
      border-top: 1px solid #E5E7EB;
      padding: 24px 48px;
      text-align: center;
      font-size: 12px;
      color: #6B7280;
      background: #FFFFFF;
      margin-top: auto;
      margin-left: var(--sidebar-width, 260px);
    }

    @media (max-width: 768px) {
      .page-footer {
        margin-left: 0;
        padding: 24px;
      }
    }

    .page-footer p {
      margin: 0;
    }

    .page-footer .footer-links {
      margin-bottom: 8px;
    }

    .page-footer a {
      color: #1B3679;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s;
    }

    .page-footer a:hover {
      text-decoration: underline;
      color: #111827;
    }
  </style>

</body>
</html>
