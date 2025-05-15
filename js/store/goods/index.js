function showMessage(type, message) {
    const msgDiv = document.createElement('div');
    msgDiv.className = type ==='success'? 'success-message' : 'error-message';
    msgDiv.textContent = message;

    let container = document.getElementById('message-container');
    if (!container) {
        container = document.createElement('div');
        container.id ='message-container';
        document.body.insertBefore(container, document.body.firstChild);
    }

    container.insertBefore(msgDiv, container.firstChild);

    setTimeout(() => {
        msgDiv.style.opacity = '1';
        msgDiv.style.transform = 'translateY(0)';
    }, 10);

    setTimeout(() => {
        msgDiv.style.opacity = '0';
        msgDiv.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            if (msgDiv.parentNode) {
                msgDiv.parentNode.removeChild(msgDiv);
            }
            if (container.children.length === 0) {
                container.parentNode.removeChild(container);
            }
        }, 300);
    }, 3000);
}

function deleteCommodity(id) {
    if (confirm('Are you sure you want to delete this commodity? This operation is irrevocable')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleteId';
        input.value = id;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteSelectedCommodities() {
    const selectedCheckboxes = document.querySelectorAll('input.select-item:checked');
    if (selectedCheckboxes.length === 0) {
        alert('Please select at least one commodity to delete.');
        return;
    }
    if (confirm('Are you sure you want to delete the selected commodities? This operation is irrevocable')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleteSelected';
        input.value = 'true';

        const selectedIds = [];
        selectedCheckboxes.forEach((checkbox) => {
            selectedIds.push(checkbox.value);
        });

        const selectedIdsInput = document.createElement('input');
        selectedIdsInput.type = 'hidden';
        selectedIdsInput.name ='selected_ids[]';
        selectedIdsInput.value = selectedIds.join(',');

        form.appendChild(input);
        form.appendChild(selectedIdsInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function selectAllCheckboxes() {
    const checkboxes = document.querySelectorAll('input.select-item');
    checkboxes.forEach((checkbox) => {
        checkbox.checked = true;
    });
}

function unselectAllCheckboxes() {
    const checkboxes = document.querySelectorAll('input.select-item');
    checkboxes.forEach((checkbox) => {
        checkbox.checked = false;
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab');
    const subTabs = document.querySelectorAll('.sub-tab');
    const addBtn = document.querySelector('.add-btn');
    const refreshBtn = document.querySelector('.refresh-btn');
    const searchBtn = document.querySelector('.search-btn');
    const overlay = document.querySelector('.overlay');
    const editBtns = document.querySelectorAll('.edit-btn');
    const closeEditFormBtn = document.querySelector('.edit-form button:last-child');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const tabText = this.textContent.trim();
            let targetUrl;
            if (tabText === 'Home') {
                targetUrl = '../../index.php';
            } else if (tabText === 'Administrator') {
                targetUrl = '../manage/user/index.php';
            } else if (tabText === 'Commodity') {
                targetUrl = window.location.href;
            }
            window.location.href = targetUrl;
            tabs.forEach(function (t) {
                t.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    subTabs.forEach(function (subTab) {
        subTab.addEventListener('click', function (e) {
            e.preventDefault();
            const subtabText = this.textContent.trim();
            let subtargetUrl;
            if (subtabText === 'List') {
                subtargetUrl = window.location.href;
            } else if (subtabText === 'Create') {
                subtargetUrl = 'create.php';
            } else if (subtabText === 'Category') {
                subtargetUrl = 'category/index.php';
            }
            window.location.href = subtargetUrl;
            subTabs.forEach(function (t) {
                t.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    addBtn.addEventListener('click', function () {
        window.location.href = 'create.php';
    });

    refreshBtn.addEventListener('click', function () {
        window.location.href = window.location.pathname;
    });

    searchBtn.addEventListener('click', function () {
        const searchInput = document.querySelector('input[name="search"]');
        const searchValue = searchInput.value.trim();
        const currentUrl = new URL(window.location.href);

        if (searchValue) {
            currentUrl.searchParams.set('search', searchValue);
        } else {
            currentUrl.searchParams.delete('search');
        }
        window.location.href = currentUrl.href;
    });


    editBtns.forEach((editBtn) => {
        editBtn.addEventListener('click', function () {
            const row = this.closest('tr');
            const id = row.dataset.id;
            const name = row.querySelector('td:nth-child(4)').textContent;
            const price = row.querySelector('td:nth-child(5)').textContent;
            const sales = row.querySelector('td:nth-child(6)').textContent;
            const category = row.querySelector('td:nth-child(7)').textContent;
            const inventory = row.querySelector('td:nth-child(8)').textContent;
            const status = row.querySelector('td:nth-child(9)').textContent;
            const image = row.querySelector('td:nth-child(3) img').src;

            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editPrice').value = price;
            document.getElementById('editSales').value = sales;
            document.getElementById('editCategory').value = category;
            document.getElementById('editInventory').value = inventory;
            document.getElementById('editStatus').value = status;
            document.getElementById('editImage').value = image;

            const imageContainer = document.getElementById('image-container');
            imageContainer.innerHTML = `<img src="${image}" alt="${name}" width="50">
                                        <button type="button" id="delete-image-btn">Delete Image</button>`;

            document.getElementById('editForm').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';

            const deleteImageBtn = document.getElementById('delete-image-btn');
            deleteImageBtn.addEventListener('click', function () {
                imageContainer.innerHTML = `<input type="file" id="upload-image" accept=".png,.jpg,.jpeg">`;
                document.getElementById('editImage').value = '';
            });
        });
    });

    closeEditFormBtn.addEventListener('click', function () {
        document.getElementById('editForm').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    });

    document.getElementById('editCommodityForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const editId = document.getElementById('editId').value;
        const editName = document.getElementById('editName').value;
        const editPrice = document.getElementById('editPrice').value;
        const editSales = document.getElementById('editSales').value;
        const editCategory = document.getElementById('editCategory').value;
        const editInventory = document.getElementById('editInventory').value;
        const editStatus = document.getElementById('editStatus').value;
        const fileInput = document.getElementById('upload-image');
        const file = fileInput? fileInput.files[0] : null;
        const currentImage = document.getElementById('editImage').value;

        const formData = new FormData();
        formData.append('id', editId);
        formData.append('name', editName);
        formData.append('price', editPrice);
        formData.append('sales', editSales);
        formData.append('category', editCategory);
        formData.append('inventory', editInventory);
        formData.append('status', editStatus);

        if (file) {
            formData.append('image', file);
        } else {
            formData.append('image', currentImage);
        }

        fetch('update_image.php', {
            method: 'POST',
            body: formData
        })
       .then(response => response.json())
       .then(data => {
            if (data.success) {
                showMessage('success', 'Commodity updated successfully.');
                window.location.href = window.location.pathname;
            } else {
                showMessage('error', 'Error updating commodity: ' + data.message);
            }
        })
       .catch(error => {
            showMessage('error', 'Error updating commodity: ' + error.message);
        });

        document.getElementById('editForm').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    });
});