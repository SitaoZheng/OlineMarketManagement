function showMessage(type, message) {
    const msgDiv = document.createElement('div');
    msgDiv.className = type === 'success' ? 'success-message' : 'error-message';
    msgDiv.textContent = message;
    
    let container = document.getElementById('message-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'message-container';
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

function deleteAdmin(id, username) {
    if (confirm(`Are you sure you want to delete "${username}"? This operation is irrevocable`)) {
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

document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab');
    const subTabs = document.querySelectorAll('.sub-tab');
    const addBtn = document.querySelector('.add-btn');
    const refreshBtn = document.querySelector('.refresh-btn');
    const searchBtn = document.querySelector('.search-btn');
    const addForm = document.querySelector('.add-form');
    const overlay = document.querySelector('.overlay');
    const editBtns = document.querySelectorAll('.edit-btn');
    const closeAddFormBtn = addForm.querySelector('button:last-child');
    const closeEditFormBtn = document.querySelector('.edit-form button:last-child');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const tabText = this.textContent.trim();
            let targetUrl;
            if (tabText === 'Home') {
                targetUrl = '../../../index.php';
            } else if (tabText === 'Administrator') {
                targetUrl = window.location.href;
            } else if (tabText === 'Commodity') {
                targetUrl = '../../goods/index.php';
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
            } else if (subtabText === 'Role') {
                subtargetUrl = '../role/index.php';
            }
            window.location.href = subtargetUrl;
            subTabs.forEach(function (t) {
                t.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    addBtn.addEventListener('click', function () {
        showAddForm();
    });

    refreshBtn.addEventListener('click', function() {
        window.location.href = window.location.pathname;
    });

    searchBtn.addEventListener('click', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const searchValue = searchInput.value.trim();
        const currentUrl = new URL(window.location.href);
        
        if (searchValue) {
            currentUrl.searchParams.set('search', searchValue);
        } else {
            currentUrl.searchParams.delete('search');
        }
        
        window.location.href = currentUrl.toString();
    });

    document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBtn.click();
        }
    });

    editBtns.forEach(function (editBtn) {
        editBtn.addEventListener('click', function () {
            const row = this.closest('tr');
            showEditForm(row);
        });
    });

    closeAddFormBtn.addEventListener('click', function () {
        hideAddForm();
    });

    closeEditFormBtn.addEventListener('click', function () {
        hideEditForm();
    });

    overlay.addEventListener('click', function () {
        hideAddForm();
        hideEditForm();
    });

    function showAddForm() {
        addForm.style.display = 'block';
        overlay.style.display = 'block';
        document.getElementById('addAdminForm').reset();
        document.getElementById('usernameError').style.display = 'none';
        document.getElementById('passwordError').style.display = 'none';
    }

    function hideAddForm() {
        addForm.style.display = 'none';
        overlay.style.display = 'none';
    }

    function showEditForm(row) {
        const username = row.cells[1].textContent;
        const role = row.cells[2].textContent;
        const password = row.cells[3].textContent;
        
        const editForm = document.querySelector('.edit-form');
        editForm.querySelector('input[name="editUsername"]').value = username;
        editForm.querySelector('select[name="editRole"]').value = role;
        editForm.querySelector('input[name="editPassword"]').value = password;
        editForm.style.display = 'block';
        overlay.style.display = 'block';
    }

    function hideEditForm() {
        document.querySelector('.edit-form').style.display = 'none';
        overlay.style.display = 'none';
    }

    document.getElementById('add_username').addEventListener('blur', function() {
        const username = this.value.trim();
        const errorDiv = document.getElementById('usernameError');
        
        if (username === '') {
            errorDiv.style.display = 'none';
            return;
        }

        fetch('../../../ajax/check_username.php?username=' + encodeURIComponent(username))
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    errorDiv.style.display = 'block';
                } else {
                    errorDiv.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error checking username:', error);
            });
    });

    document.getElementById('add_confirmPassword').addEventListener('input', function() {
        const password = document.getElementById('add_password').value;
        const confirmPassword = this.value;
        const errorDiv = document.getElementById('passwordError');
        
        if (password !== confirmPassword) {
            errorDiv.style.display = 'block';
        } else {
            errorDiv.style.display = 'none';
        }
    });
});