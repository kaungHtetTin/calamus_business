(function () {
  document.querySelectorAll("[data-password-toggle]").forEach(function (button) {
    button.addEventListener("click", function () {
      var input = document.getElementById(button.dataset.passwordToggle);
      if (!input) return;

      var isVisible = input.type === "text";
      input.type = isVisible ? "password" : "text";
      button.setAttribute("aria-label", isVisible ? "Show password" : "Hide password");

      var icon = button.querySelector("i");
      if (icon) {
        icon.classList.toggle("fa-eye", isVisible);
        icon.classList.toggle("fa-eye-slash", !isVisible);
      }
    });
  });

  var password = document.getElementById("password");
  var requirement = document.querySelector("[data-password-requirement]");

  if (password && requirement) {
    var updateRequirement = function () {
      requirement.classList.toggle("is-valid", password.value.length >= 8);
    };

    password.addEventListener("input", updateRequirement);
    updateRequirement();
  }
})();
