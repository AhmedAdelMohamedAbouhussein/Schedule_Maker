
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
