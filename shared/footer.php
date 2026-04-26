<body class="d-flex flex-column min-vh-100">
  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-3 mt-auto">
    <p>
      &copy; <?php echo date("Y"); ?> WatAtlas.com |
      <a href="#" class="text-white" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a> |
      <a href="#" class="text-white" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a>
    </p>
  </footer>

  <!-- Privacy Policy Modal -->
  <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Replace with your actual privacy policy content -->
          <p>This is where your Privacy Policy text goes. You can paste the full content here or load it dynamically.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Terms of Service Modal -->
  <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="termsModalLabel">Terms of Service</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Replace with your actual terms of service content -->
          <p>This is where your Terms of Service text goes. You can paste the full content here or load it dynamically.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS (with Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 