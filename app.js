
function sendAction(macAddress, ipAddress, hostName, action, pw) {
    if (action === 'Restart' || action === 'Shutdown') {
        const password = prompt(`Enter the password to ${action.toLowerCase()} this host:`);
        if (password !== pw) {
            showAlert('Incorrect password. Action cancelled.', false);
            return;
        }
    }

    const data = {
        host: { macAddress, ipAddress, hostName },
        action: action
    };

    fetch('wol_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'Error') {
            showAlert(data.error, false);
        } else if (data.status === 'OK') {
            showAlert(data.message, true);
        } else {
            showAlert('Unexpected response from server', false);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        showAlert('An unexpected error occurred', false);
    });
}

function showAlert(message, isSuccess) {
    const alertElement = document.getElementById('globalAlert');
    const alertMessage = document.getElementById('alertMessage');
    
    alertElement.className = `alert alert-dismissible fade show alert-${isSuccess ? 'success' : 'danger'}`;
    alertMessage.textContent = message;
    alertElement.style.display = 'block';

    setTimeout(() => {
        hideAlert();
    }, 5000);
}

function hideAlert() {
    const alertElement = document.getElementById('globalAlert');
    alertElement.style.display = 'none';
}
