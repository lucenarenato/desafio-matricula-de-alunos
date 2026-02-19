document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("theme-toggle");
    const themeToggleText = document.getElementById("theme-toggle-text");
    const themeToggleDarkIcon = document.getElementById("theme-toggle-dark-icon");
    const themeToggleLightIcon = document.getElementById("theme-toggle-light-icon");

    // Verifica se os elementos existem antes de executar qualquer ação
    if (themeToggle && themeToggleDarkIcon && themeToggleLightIcon) {
        // Aplica o tema imediatamente ao carregar a página
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.classList.add("dark");
            if (themeToggleText) themeToggleText.textContent = "Desativar";
            themeToggleDarkIcon.classList.remove("hidden");
            themeToggleLightIcon.classList.add("hidden");
        } else {
            document.documentElement.classList.remove("dark");
            if (themeToggleText) themeToggleText.textContent = "Ativar";
            themeToggleDarkIcon.classList.add("hidden");
            themeToggleLightIcon.classList.remove("hidden");
        }

        // Alternar tema ao clicar no botão
        themeToggle.addEventListener("click", function () {
            if (document.documentElement.classList.contains("dark")) {
                document.documentElement.classList.remove("dark");
                localStorage.setItem("theme", "light");
                if (themeToggleText) themeToggleText.textContent = "Ativar";
                themeToggleDarkIcon.classList.add("hidden");
                themeToggleLightIcon.classList.remove("hidden");
            } else {
                document.documentElement.classList.add("dark");
                localStorage.setItem("theme", "dark");
                if (themeToggleText) themeToggleText.textContent = "Desativar";
                themeToggleDarkIcon.classList.remove("hidden");
                themeToggleLightIcon.classList.add("hidden");
            }
        });
    }
});
