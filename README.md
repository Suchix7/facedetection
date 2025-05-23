# Face Attendance System

A web-based face attendance system that uses Haar Cascade face detection to mark attendance through a web interface.

## Requirements

- PHP 7.4 or higher
- Python 3.6 or higher
- OpenCV for Python (pip install opencv-python)
- Web server (Apache/Nginx)
- Modern web browser with camera access

## Installation

1. Clone or download this repository to your web server directory

2. Install Python dependencies:

   ```bash
   pip install opencv-python
   ```

3. Make sure the `uploads` directory is writable:

   ```bash
   chmod 777 uploads
   ```

4. Ensure the `haarcascade_frontalface_default.xml` file is present in the root directory

5. Make sure Python is accessible from PHP (check your server's PATH)

## Usage

1. Open the web interface in your browser
2. Click "Start Camera" to begin
3. Position your face in the camera view
4. Click "Capture & Mark Attendance" to mark your attendance
5. The system will detect your face and record your attendance
6. View the attendance records in the right panel

## Features

- Real-time face detection
- Web-based interface
- Attendance history tracking
- Responsive design
- Simple and intuitive UI

## Security Notes

- This is a basic implementation and should be enhanced with proper authentication in a production environment
- Consider implementing face recognition for user identification
- Add proper access controls and data validation
- Implement secure storage for attendance records

## Troubleshooting

If you encounter any issues:

1. Make sure Python and OpenCV are properly installed
2. Check if the `uploads` directory has proper write permissions
3. Verify that the Haar Cascade XML file is present
4. Check your server's error logs for any PHP or Python errors

## License

MIT License
"# facedetection" 
