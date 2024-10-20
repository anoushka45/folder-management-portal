
document.getElementById('uploadForm').addEventListener('submit', function (event) {
  event.preventDefault();  // Prevent the form from submitting the traditional way

  var form = document.getElementById('uploadForm');
  var formData = new FormData(form);

  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'upload.php', true);

  // Show the progress bar
  document.getElementById('progressContainer').style.display = 'block';

  // Update progress bar
  xhr.upload.onprogress = function (event) {
    if (event.lengthComputable) {
      var percentComplete = Math.round((event.loaded / event.total) * 100);
      document.getElementById('uploadProgress').value = percentComplete;
      document.getElementById('progressText').textContent = percentComplete + '%';
    }
  };

  // Handle the response after the upload is complete
  xhr.onload = function () {
    if (xhr.status === 200) {
      // Upload successful, refresh the page
      location.reload();
    } else {
      // Handle error response here
      alert('An error occurred during the upload.');
    }
  };

  // Send the form data via AJAX
  xhr.send(formData);
});


function toggleCheckboxes(selectAllCheckbox, checkboxClass) {
  const checkboxes = document.querySelectorAll(`.${checkboxClass}`);
  checkboxes.forEach(checkbox => {
    checkbox.checked = selectAllCheckbox.checked;
  });
}

function downloadSelected(type) {
  const form = type === 'image' ? document.getElementById('imageForm') : document.getElementById('videoForm');
  const checkboxes = form.querySelectorAll(`input[type="checkbox"]:checked`);
  if (checkboxes.length > 0) {
    const selectedFiles = Array.from(checkboxes).map(checkbox => checkbox.value);
    // Create a form to submit the selected files for downloading
    const downloadForm = document.createElement('form');
    downloadForm.method = 'post';
    downloadForm.action = 'download_selected_media.php';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = type === 'image' ? 'selected_items' : 'selected_videos';
    input.value = JSON.stringify(selectedFiles);
    downloadForm.appendChild(input);

    document.body.appendChild(downloadForm);
    downloadForm.submit();
  } else {
    alert('Please select at least one item to download.');
  }
}



document.querySelectorAll('.video-container video').forEach(function (video) {
  video.addEventListener('play', function () {
    video.style.objectFit = 'contain'; // Resets object-fit when playing
  });

  video.addEventListener('pause', function () {
    video.style.objectFit = 'contain'; // Resets back to cover when paused
  });

  video.addEventListener('ended', function () {
    video.style.objectFit = 'cover'; // Resets back to cover when video ends
  });
});

function filterMedia() {
  var filter = document.getElementById('mediaFilter').value;
  var imageSection = document.getElementById('imageSection');
  var videoSection = document.getElementById('videoSection');

  if (filter === 'all') {
    imageSection.style.display = 'block';
    videoSection.style.display = 'block';
  } else if (filter === 'images') {
    imageSection.style.display = 'block';
    videoSection.style.display = 'none';
  } else if (filter === 'videos') {
    imageSection.style.display = 'none';
    videoSection.style.display = 'block';
  }
}

// Initialize by displaying all media
filterMedia();

$(document).ready(function () {
  // Function to handle click on image in grid view
  $('.card-img-top').click(function () {
    var imageSrc = $(this).attr('src');
    showSlideshowModal(imageSrc);
  });

  // Function to handle click on file name in list view
  window.openSlideshowModal = function (imageSrc) {
    showSlideshowModal(imageSrc);
  };

  // Common function to show the slideshow modal
  function showSlideshowModal(imageSrc) {
    var img = $('<img>').attr('src', imageSrc).addClass('d-block w-100');
    var item = $('<div>').addClass('carousel-item active').append(img);
    $('#slideshowModal .carousel-inner').empty().append(item);
    $('#slideshowModal').modal('show');
  }

  // Function to handle click on video title

  // Function to handle click on download button


  // Handle file upload progress
  const uploadForm = document.getElementById('uploadForm');
  const progressContainer = document.getElementById('progressContainer');
  const uploadProgress = document.getElementById('uploadProgress');
  const progressText = document.getElementById('progressText');

  if (uploadForm) {
    uploadForm.addEventListener('submit', function (event) {
      event.preventDefault();

      const formData = new FormData(uploadForm);
      const xhr = new XMLHttpRequest();

      xhr.open('POST', uploadForm.action, true);

      // Show progress bar
      progressContainer.style.display = 'block';

      // Update progress bar
      xhr.upload.addEventListener('progress', function (e) {
        if (e.lengthComputable) {
          const percentComplete = Math.round((e.loaded / e.total) * 100);
          uploadProgress.value = percentComplete;
          progressText.textContent = percentComplete + '%';
        }
      });

      xhr.addEventListener('load', function () {
        if (xhr.status === 200) {
          // File uploaded successfully
          progressText.textContent = 'Upload complete!';
        } else {
          // File upload failed
          progressText.textContent = 'Upload failed!';
        }
      });

      xhr.send(formData);
    });
  }
});
// Hide context menus on click outside
document.addEventListener('click', function (event) {
  if (!event.target.closest('.context-menu')) {
    document.querySelectorAll('.context-menu').forEach(menu => menu.style.display = 'none');
  }
});

// Show context menu for subfolders
function showSubfolderContextMenu(event, subfolderId) {
  const menu = document.getElementById('context-menu-subfolder-' + subfolderId);
  if (menu) {
    menu.style.top = `${event.pageY}px`;
    menu.style.left = `${event.pageX}px`;
    menu.style.display = 'block';
  }
}

// Rename subfolder function
function renameSubfolder(subfolderId) {
  const newName = prompt("Enter new subfolder name:");
  if (newName) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'renamefolder.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('event_id=' + subfolderId + '&new_name=' + encodeURIComponent(newName));

    xhr.onload = function () {
      if (xhr.status === 200) {
        location.reload(); // Refresh the page to see changes
      } else {
        alert('Error renaming subfolder');
      }
    };
  }
}

// Delete subfolder function
function deleteSubfolder(subfolderId) {
  if (confirm("Are you sure you want to delete this subfolder and ALL of its contents?")) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'deletefolder.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('event_id=' + subfolderId);

    xhr.onload = function () {
      if (xhr.status === 200) {
        location.reload(); // Refresh the page to see changes
      } else {
        alert('Error deleting subfolder');
      }
    };
  }
}





