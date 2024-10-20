document.addEventListener('click', function(event) {
if (!event.target.closest('.context-menu')) {
document.querySelectorAll('.context-menu').forEach(menu => menu.style.display = 'none');
}
});

function showContextMenu(event, folderId) {
const menu = document.getElementById('context-menu-' + folderId);
if (menu) {
menu.style.top = `${event.pageY}px`;
menu.style.left = `${event.pageX}px`;
menu.style.display = 'block';
}
}

function renameFolder(folderId) {
const newName = prompt("Enter new folder name:");
if (newName) {
const xhr = new XMLHttpRequest();
xhr.open('POST', 'renamefolder.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
xhr.send('event_id=' + folderId + '&new_name=' + encodeURIComponent(newName));

xhr.onload = function() {
if (xhr.status === 200) {
    location.reload(); // Refresh the page to see changes
} else {
    alert('Error renaming folder');
}
};
}
}

function deleteFolder(folderId) {
if (confirm("Are you sure you want to delete this folder and ALL of its contents?")) {
const xhr = new XMLHttpRequest();
xhr.open('POST', 'deletefolder.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
xhr.send('event_id=' + folderId);

xhr.onload = function() {
if (xhr.status === 200) {
    location.reload(); // Refresh the page to see changes
} else {
    alert('Error deleting folder');
}
};
}
}


