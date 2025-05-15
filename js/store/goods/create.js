document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab');
    const subTabs = document.querySelectorAll('.sub-tab');

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
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
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    subTabs.forEach(subTab => {
        subTab.addEventListener('click', function(e) {
            e.preventDefault();
            const subtabText = this.textContent.trim();
            let subtargetUrl;
            if (subtabText === 'List') {
                subtargetUrl = 'index.php';
            } else if (subtabText === 'Create') {
                subtargetUrl = window.location.href;
            } else if (subtabText === 'Category') {
                subtargetUrl = 'category/index.php';
            }
            window.location.href = subtargetUrl;
            subTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    document.getElementById('image-upload-area').addEventListener('click', function() {
        document.getElementById('upload-image').click();
    });

    const fileInput = document.getElementById('upload-image');
    const preview = document.getElementById('image-preview');
    const uploadArea = document.getElementById('image-upload-area');
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                uploadArea.style.display = 'none';
                preview.style.display = 'block';
                
                document.getElementById('preview-image').src = e.target.result;
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });

    window.clearImagePreview = function() {
        uploadArea.style.display = 'flex';
        preview.style.display = 'none';
        
        fileInput.value = '';
    }

    const form = document.getElementById('createCommodityForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }
        
        const createName = document.getElementById('createName').value;
        const createPrice = document.getElementById('createPrice').value;
        const createSales = document.getElementById('createSales').value;
        const createCategory = document.getElementById('createCategory').value;
        const createInventory = document.getElementById('createInventory').value;
        const createStatus = document.getElementById('createStatus').value;
        const fileInput = document.getElementById('upload-image');
        const file = fileInput?.files[0];

        const formData = new FormData();

        formData.append('name', createName);
        formData.append('price', createPrice);
        formData.append('sales', createSales);
        formData.append('category', createCategory);
        formData.append('inventory', createInventory);
        formData.append('status', createStatus);
        formData.append('image', file);

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        fetch('create_image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Server error: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;

            if (data.success) {
                showMessage('success', 'Commodity created successfully!');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            } else {
                showMessage('error', 'Error: ' + data.message);
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            showMessage('error', 'Network error: ' + error.message);
            console.error('Fetch error:', error);
        });
    });

    function validateForm() {
        const name = document.getElementById('createName').value.trim();
        const price = document.getElementById('createPrice').value;
        const sales = document.getElementById('createSales').value;
        const inventory = document.getElementById('createInventory').value;
        
        if (name === '') {
            showMessage('error', 'Item name is required');
            return false;
        }
        
        if (name.length > 200) {
            showMessage('error', 'Item name cannot exceed 200 characters');
            return false;
        }
        
        if (price <= 0) {
            showMessage('error', 'Price must be a positive number');
            return false;
        }
        
        if (sales < 0) {
            showMessage('error', 'VOL cannot be negative');
            return false;
        }
        
        if (inventory < 0) {
            showMessage('error', 'Inventory cannot be negative');
            return false;
        }
        
        return true;
    }

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
});