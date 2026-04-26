<body class="d-flex flex-column min-vh-100">

<!-- FOOTER -->
<footer class="bg-dark text-white py-3 mt-auto">
  <div class="container d-flex justify-content-center align-items-center flex-wrap gap-2">

    <small class="mb-0">
      &copy; <?php echo date("Y"); ?> WatAtlas.com
    </small>

    <span class="text-white-50">|</span>

    <a href="#" class="text-white text-decoration-none small"
       data-bs-toggle="modal" data-bs-target="#privacyModal">
      Privacy Policy
    </a>

    <span class="text-white-50">|</span>

    <a href="#" class="text-white text-decoration-none small"
       data-bs-toggle="modal" data-bs-target="#termsModal">
      Terms of Service
    </a>

  </div>
</footer>

<!-- =========================
     PRIVACY POLICY MODAL
========================= -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header bg-light">
        <h5 class="modal-title fw-semibold">Privacy Policy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body px-4 py-3">

        <p class="text-muted mb-4">Effective Date: March 2026</p>

        <h6 class="fw-bold mt-3">Information We Collect</h6>
        <p>
          When you sign in using Google authentication, we receive your name and email address.
          We do not collect sensitive login credentials.
        </p>

        <h6 class="fw-bold mt-4">How We Use Your Information</h6>
        <ul>
          <li>Account creation and management</li>
          <li>Personalized user experience</li>
          <li>Support and communication</li>
        </ul>

        <h6 class="fw-bold mt-4">Data Security</h6>
        <p>Your data is securely stored using standard security practices.</p>

        <h6 class="fw-bold mt-4">Sharing</h6>
        <p>We do not sell or share your personal data except when required by law.</p>

        <h6 class="fw-bold mt-4">Your Rights</h6>
        <p>
          You can request data access or deletion at
          <a href="mailto:support@watatlas.com">support@watatlas.com</a>.
        </p>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- =========================
     TERMS MODAL
========================= -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header bg-light">
        <h5 class="modal-title fw-semibold">Terms of Service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body px-4 py-3">

        <p class="text-muted mb-4">Effective Date: March 2026</p>

        <h6 class="fw-bold mt-3">Acceptance</h6>
        <p>By using WatAtlas, you agree to these terms.</p>

        <h6 class="fw-bold mt-4">User Responsibilities</h6>
        <p>You must use the platform legally and responsibly.</p>

        <h6 class="fw-bold mt-4">Content</h6>
        <p>Users are responsible for their posted content.</p>

        <h6 class="fw-bold mt-4">Intellectual Property</h6>
        <p>All content belongs to WatAtlas and may not be copied.</p>

        <h6 class="fw-bold mt-4">Liability</h6>
        <p>WatAtlas is provided “as is” without warranties.</p>

        <h6 class="fw-bold mt-4">Termination</h6>
        <p>Accounts may be suspended for violations.</p>

        <h6 class="fw-bold mt-4">Changes</h6>
        <p>We may update these terms at any time.</p>

        <h6 class="fw-bold mt-4">Law</h6>
        <p>Governed by the laws of Mauritius.</p>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>