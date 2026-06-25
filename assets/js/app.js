// assets/js/app.js

document.addEventListener("DOMContentLoaded", () => {
    // 1. Copy to Clipboard Functionality
    const copyBtns = document.querySelectorAll(".copy-link-btn");
    copyBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            const inputId = btn.getAttribute("data-target");
            const inputEl = document.getElementById(inputId);
            if (inputEl) {
                inputEl.select();
                inputEl.setSelectionRange(0, 99999); // For mobile devices
                navigator.clipboard.writeText(inputEl.value).then(() => {
                    const originalText = btn.textContent;
                    btn.textContent = "Copied!";
                    btn.style.color = "#10b981"; // Success color
                    setTimeout(() => {
                        btn.textContent = originalText;
                        btn.style.color = "";
                    }, 2000);
                }).catch(err => {
                    console.error("Failed to copy text: ", err);
                });
            }
        });
    });

    // 2. Real-Time Table Searching / Filtering
    const searchInputs = document.querySelectorAll(".table-search");
    searchInputs.forEach(input => {
        input.addEventListener("keyup", () => {
            const filter = input.value.toLowerCase();
            const targetTableId = input.getAttribute("data-table");
            const table = document.getElementById(targetTableId);
            if (table) {
                const trs = table.getElementsByTagName("tr");
                // Skip the first row (header)
                for (let i = 1; i < trs.length; i++) {
                    let match = false;
                    const tds = trs[i].getElementsByTagName("td");
                    for (let j = 0; j < tds.length; j++) {
                        if (tds[j]) {
                            const textVal = tds[j].textContent || tds[j].innerText;
                            if (textVal.toLowerCase().indexOf(filter) > -1) {
                                match = true;
                                break;
                            }
                        }
                    }
                    trs[i].style.display = match ? "" : "none";
                }
            }
        });
    });

    // 3. Modal Popup Handlers
    const modalTriggers = document.querySelectorAll("[data-open-modal]");
    modalTriggers.forEach(trigger => {
        trigger.addEventListener("click", (e) => {
            e.preventDefault();
            const modalId = trigger.getAttribute("data-open-modal");
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add("active");
                // If opening meeting feedback, we can inject the meeting_id into the form
                const meetingId = trigger.getAttribute("data-meeting-id");
                const hiddenInput = modal.querySelector("input[name='meeting_id']");
                if (hiddenInput && meetingId) {
                    hiddenInput.value = meetingId;
                }
            }
        });
    });

    const closeModalBtns = document.querySelectorAll(".close-modal, .modal-close-trigger");
    closeModalBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            const modal = btn.closest(".modal");
            if (modal) {
                modal.classList.remove("active");
            }
        });
    });

    // Close modal when clicking outside content
    window.addEventListener("click", (e) => {
        if (e.target.classList.contains("modal")) {
            e.target.classList.remove("active");
        }
    });

    // 4. Client Side Validations (Real-time checks)
    const formsToValidate = document.querySelectorAll(".needs-validation");
    formsToValidate.forEach(form => {
        form.addEventListener("submit", (e) => {
            let valid = true;
            const requiredInputs = form.querySelectorAll("[required]");
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = "#ef4444";
                    // Add micro-animation shake class
                    input.classList.add("shake-effect");
                    setTimeout(() => input.classList.remove("shake-effect"), 500);
                } else {
                    input.style.borderColor = "";
                }
            });

            // Password confirmations match
            const password = form.querySelector("input[name='password']");
            const confirm = form.querySelector("input[name='confirm_password']");
            if (password && confirm && password.value !== confirm.value) {
                valid = false;
                confirm.style.borderColor = "#ef4444";
                alert("Passwords do not match!");
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    });

    // 5. Theme Switcher & Initialization
    const currentTheme = localStorage.getItem("theme");
    const toggleBtn = document.getElementById("dark-mode-toggle");
    
    if (currentTheme === "dark") {
        document.body.classList.add("dark-mode");
        if (toggleBtn) {
            const icon = toggleBtn.querySelector("i");
            if (icon) {
                icon.classList.remove("fa-moon");
                icon.classList.add("fa-sun");
            }
        }
    }
    
    if (toggleBtn) {
        toggleBtn.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");
            const isDark = document.body.classList.contains("dark-mode");
            localStorage.setItem("theme", isDark ? "dark" : "light");
            
            const icon = toggleBtn.querySelector("i");
            if (icon) {
                if (isDark) {
                    icon.classList.remove("fa-moon");
                    icon.classList.add("fa-sun");
                } else {
                    icon.classList.remove("fa-sun");
                    icon.classList.add("fa-moon");
                }
            }
        });
    }

    // 6. Responsive Sidebar Drawer Handler
    const sidebar = document.querySelector(".sidebar");
    const sidebarOverlay = document.getElementById("sidebar-overlay");
    const sidebarToggle = document.getElementById("sidebar-toggle");
    const sidebarClose = document.getElementById("sidebar-close");
    
    function toggleSidebar() {
        if (sidebar && sidebarOverlay) {
            sidebar.classList.toggle("active");
            sidebarOverlay.classList.toggle("active");
        }
    }
    
    function closeSidebar() {
        if (sidebar && sidebarOverlay) {
            sidebar.classList.remove("active");
            sidebarOverlay.classList.remove("active");
        }
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", toggleSidebar);
    }
    
    if (sidebarClose) {
        sidebarClose.addEventListener("click", closeSidebar);
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", closeSidebar);
    }
    
    // Auto-close sidebar on navigation
    const sidebarLinks = document.querySelectorAll(".sidebar-item a");
    sidebarLinks.forEach(link => {
        link.addEventListener("click", closeSidebar);
    });
});
