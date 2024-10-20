
# Folder and Media Management System

This project is a **Folder and Media Management System** designed to streamline the process of managing and sharing media files (photos and videos) among three types of users: **Committee**, **Admin**, and **End User**. The system facilitates media upload, review, and approval, along with providing notifications and media sharing capabilities for all users.

## Features Overview

### 1. Committee
- **Upload Media**: Committees can upload photos and videos into specific folders.
- **Download & Share**: Committee members can download and share the media they have uploaded.
- **Notifications**: Committees receive notifications if any media item is rejected by the Admin.
- **Restore Requests**: Committee members can request the Admin to restore a media item that has been previously rejected.
  
### 2. Admin
- **Notifications**: 
  - Admin receives notifications when a new folder is added by a Committee.
  - Admin also gets notifications for restore requests from the Committee.
- **Review Media**:
  - Admin can approve or reject media uploaded by Committees.
  - Admin can restore previously rejected media based on Committee requests.
- **Reject Media**: Admin can reject media items, which will notify the Committee.
  
### 3. End User
- **View Approved Media**: End users can only see media items that have been approved by the Admin.
- **Download & Share**: End users can download and share approved media.

### Shared Functionality
- **Download & Share Media**: All users (Committee, Admin, End User) have the ability to download and share media.
  
## System Workflow

1. **Committee Actions**:
   - Upload media files (photos/videos) into folders.
   - Can download, share, and organize their media.
   - Receive notifications when any media is rejected by the Admin.
   - Can request the Admin to restore rejected media items.

2. **Admin Actions**:
   - Receives notifications when a new folder is added or a restore request is made.
   - Can reject media uploaded by Committees with reasons.
   - Reviews and approves or rejects media restoration requests from Committees.

3. **End User Actions**:
   - Only sees media that has been approved by the Admin.
   - Can download and share approved media files.
   
4. **Home Page**:
   - Only sees media that has been approved by the Admin to get a glimpse.
   

## Project Structure

The system is built with the following key features:
- **Folder Management**: Committees can create and organize folders.
- **Media Management**: Photos and videos can be uploaded, reviewed, and restored based on user roles.
- **Notification System**: Each type of user receives relevant notifications based on the actions performed by others.
  
## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/folder-management-system.git
   ```

2. Move the project into your XAMPP `htdocs` directory:
   ```bash
   mv folder-management-system /path/to/xampp/htdocs/
   ```

3. Import the provided database schema into your MySQL server.

4. Access the system via `http://localhost/folder-management-system`.

## Technologies Used
- **PHP**: Backend logic and server-side scripting.
- **MySQL**: Database for managing users, media, and folders.
- **JavaScript / jQuery**: Interactive frontend for media management and notifications.
- **Bootstrap**: Responsive UI design.
- **HTML/CSS**: Structure and styling of the system.






