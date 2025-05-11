
    window.addEventListener("load", () => {
        const preloader = document.getElementById("preloader");
        const mainContent = document.getElementById("main-content");

      // After 2.2s of letter animation, zoom in
        setTimeout(() => {
        preloader.classList.add("zoom-out");
        }, 2200);

      // After zoom finishes, it's gonna show the mainpage
        setTimeout(() => {
        preloader.style.display = "none";
        mainContent.style.display = "block";
        }, 3600);
    });

    // Language toggle functionality (just a demo)
    document.getElementById("lang-ar").addEventListener("click", (e) => {
        e.preventDefault();
      // For demo: Toggle active state (replace with actual translation logic)
        document.getElementById("lang-en").classList.remove("active");
        document.getElementById("lang-ar").classList.add("active");
      alert("Language switched to Arabic (demo)"); // gotta use API for it. (but im too lazy for it honestly :P)
    });

    document.getElementById("lang-en").addEventListener("click", (e) => {
        e.preventDefault();
        document.getElementById("lang-ar").classList.remove("active");
        document.getElementById("lang-en").classList.add("active");
    });