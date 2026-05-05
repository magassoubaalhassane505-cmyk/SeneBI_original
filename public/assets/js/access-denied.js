(function () {
  function getParam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
  }

  document.addEventListener("DOMContentLoaded", function () {
    const messageEl = document.querySelector("#deniedMessage");
    const backBtn = document.querySelector("#backToSpaceBtn");
    const auth = window.SeneBI?.getAuth ? window.SeneBI.getAuth() : null;

    const message =
      getParam("message") ||
      "Oups ! Cette zone est reservee aux proprietaires. Si vous avez besoin de ces chiffres, contactez votre manager.";
    if (messageEl) messageEl.textContent = message;

    if (backBtn) {
      if (auth?.role === "manager") backBtn.href = "/secure-portal";
      else backBtn.href = "/client/dashboard";
    }
  });
})();
