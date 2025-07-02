document.addEventListener("DOMContentLoaded", function () {
    console.log("JS Loaded, initializing elements...");

    // Initialize the main user's datepicker
    const birthDateInput = document.getElementById("CrudBirthDate");
    const ageDisplay = document.getElementById("CrudAgeDisplay");

    function computeAge(input, display) {
        if (!input.value) {
            display.textContent = "--";
            return;
        }

        const birthDate = new Date(input.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        display.textContent = age >= 0 ? age : "--";
    }

    if (birthDateInput) {
        $(birthDateInput).datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            yearRange: "1900:+10",
            onSelect: function () {
                computeAge(birthDateInput, ageDisplay);
            }
        });

        birthDateInput.addEventListener("change", function () {
            computeAge(birthDateInput, ageDisplay);
        });

        // Ensure age is calculated on page load
        setTimeout(() => computeAge(birthDateInput, ageDisplay), 200);
    } else {
        console.error("❌ User Birth Date Picker not found!");
    }

    // Function to initialize a beneficiary row (both new and preloaded)
    function initializeBeneficiaryRow(row) {
        const birthDateInput = row.querySelector("input.beneficiary-birthdate"); // Updated selector
        const ageDisplay = row.querySelector(".beneficiary-age");

        if (!birthDateInput) {
            console.error("❌ Birthdate input not found in row!");
            return;
        }

        // Add datepicker
        $(birthDateInput).datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            yearRange: "1900:+10",
            onSelect: function () {
                computeAge(birthDateInput, ageDisplay);
            }
        });

        // Compute age on load
        computeAge(birthDateInput, ageDisplay);

        // Recompute age on change
        birthDateInput.addEventListener("change", function () {
            computeAge(birthDateInput, ageDisplay);
        });

        console.log("✅ Beneficiary row initialized!");
    }

    // Initialize preloaded beneficiaries
    document.querySelectorAll("#beneficiaryTable tbody tr").forEach(initializeBeneficiaryRow);

    // Add Beneficiary Button
    const addBeneficiaryButton = document.getElementById("addBeneficiary");
    if (addBeneficiaryButton) {
        addBeneficiaryButton.addEventListener("click", function () {
            console.log("✅ Add Beneficiary Clicked");
            addBeneficiaryRow();
        });
    } else {
        console.error("❌ Add Beneficiary button not found!");
    }

    function addBeneficiaryRow() {
        const tableBody = document.querySelector("#beneficiaryTable tbody");
        const rowCount = tableBody.rows.length;
        const row = tableBody.insertRow();

        row.innerHTML = `
            <td><input type="text" name="beneficiaries[${rowCount}][name]" required></td>
            <td><input type="text" class="beneficiary-birthdate" name="beneficiaries[${rowCount}][birth_date]" required></td>
            <td><span class="beneficiary-age">--</span></td>
            <td><input type="text" name="beneficiaries[${rowCount}][relationship]" required></td>
            <td>
                <button type="button" class="removeBeneficiary">Remove</button>
            </td>
        `;

        initializeBeneficiaryRow(row);

        console.log("✅ New Beneficiary row added!");
    }

    // Event Delegation for Remove Buttons
    const beneficiaryTable = document.querySelector("#beneficiaryTable tbody");
    if (beneficiaryTable) {
        beneficiaryTable.addEventListener("click", function (event) {
            if (event.target.classList.contains("removeBeneficiary")) {
                const row = event.target.closest("tr");
                const deleteFlag = row.querySelector(".delete-flag");

                if (deleteFlag) {
                    // For preloaded rows: Mark for deletion
                    deleteFlag.value = "1"; // Set to "delete"
                    row.style.display = "none"; // Hide the row
                } else {
                    // For new rows: Remove from DOM
                    row.remove();
                }
            }
        });
    } else {
        console.error("❌ Beneficiary table body not found!");
    }

    // Event Delegation for File Delete Buttons
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-file")) {
            const fileRow = event.target.closest("li");
            const deleteFlag = fileRow.querySelector(".delete-file-flag");

            if (deleteFlag) {
                // Flag the file for deletion
                deleteFlag.value = "1"; // Set to "delete"
                fileRow.style.display = "none"; // Hide the row
                console.log("✅ File marked for deletion.");
            }
        }
    });
});
