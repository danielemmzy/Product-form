// products.js

console.log("products.js loaded successfully");

// Ensure DOM is fully ready before running any logic
document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("productForm");
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    if (!form) {
        console.error("Product form not found in DOM");
        return;
    }

    // ===============================
    // FORM SUBMIT (AJAX)
    // ===============================
    form.addEventListener("submit", function (e) {
        e.preventDefault(); // STOP normal form submit

        console.log("Form submit intercepted");

        const name = document.getElementById("product_name").value.trim();
        const qty = document.getElementById("quantity").value;
        const price = document.getElementById("price").value;

        if (!name || !qty || !price) {
            alert("All fields are required");
            return;
        }

        const btn = document.getElementById("submitBtn");
        btn.disabled = true;
        btn.textContent = "Saving...";

        fetch("/products", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                product_name: name,
                quantity: qty,
                price: price
            })
        })
        .then(response => response.json())
        .then(data => {

            if (data.success) {
                console.log("Product saved:", data);

                appendRow(data.product);
                recalcGrandTotal();
                form.reset();
                showSuccess();
            } else {
                console.error("Save failed", data);
            }
        })
        .catch(error => {
            console.error("Request error:", error);
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = "Save Product";
        });
    });

});


// ===============================
// ADD ROW TO TABLE
// ===============================
function appendRow(p) {
    const tbody = document.getElementById("tableBody");

    const tr = document.createElement("tr");
    tr.id = "row-" + p.id;

    tr.innerHTML = `
        <td class="col-name">${escapeHtml(p.product_name)}</td>
        <td class="col-qty">${p.quantity}</td>
        <td class="col-price">$${p.price}</td>
        <td class="col-date">${p.created_at}</td>
        <td class="col-total">$${p.total}</td>
        <td>
            <button class="btn btn-outline-secondary btn-sm"
                onclick="startEdit(${p.id})">Edit</button>
        </td>
    `;

    tbody.appendChild(tr);
}


// ===============================
// EDIT MODE
// ===============================
function startEdit(id) {
    const row = document.getElementById("row-" + id);

    const name = row.querySelector(".col-name").textContent.trim();
    const qty = row.querySelector(".col-qty").textContent.trim();
    const price = row.querySelector(".col-price").textContent.replace("$", "").trim();

    row.querySelector(".col-name").innerHTML =
        `<input id="edit-name-${id}" value="${escapeHtml(name)}" class="form-control">`;

    row.querySelector(".col-qty").innerHTML =
        `<input id="edit-qty-${id}" value="${qty}" class="form-control">`;

    row.querySelector(".col-price").innerHTML =
        `<input id="edit-price-${id}" value="${price}" class="form-control">`;

    row.querySelector("td:last-child").innerHTML = `
        <button class="btn btn-success btn-sm me-1" onclick="saveEdit(${id})">Save</button>
        <button class="btn btn-secondary btn-sm" onclick="location.reload()">Cancel</button>
    `;
}


// ===============================
// SAVE EDIT
// ===============================
function saveEdit(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    fetch("/products/" + id, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json"
        },
        body: JSON.stringify({
            product_name: document.getElementById("edit-name-" + id).value,
            quantity: document.getElementById("edit-qty-" + id).value,
            price: document.getElementById("edit-price-" + id).value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => console.error(err));
}


// ===============================
// GRAND TOTAL
// ===============================
function recalcGrandTotal() {
    let total = 0;

    document.querySelectorAll("#tableBody tr").forEach(row => {
        const totalCell = row.querySelector("td:nth-child(5)");

        if (!totalCell) return;

        const value = parseFloat(
            totalCell.textContent.replace("$", "").trim()
        );

        if (!isNaN(value)) {
            total += value;
        }
    });

    const grandEl = document.getElementById("grand");

    if (!grandEl) {
        console.error("Grand total element (#grand) missing");
        return;
    }

    grandEl.textContent = total.toFixed(2);
}


// ===============================
// SUCCESS MESSAGE
// ===============================
function showSuccess() {
    const msg = document.getElementById("successMsg");

    if (!msg) return;

    msg.style.display = "block";

    setTimeout(() => {
        msg.style.display = "none";
    }, 3000);
}


// ===============================
// SAFE HTML ESCAPE
// ===============================
function escapeHtml(str) {
    const div = document.createElement("div");
    div.innerText = str;
    return div.innerHTML;
}