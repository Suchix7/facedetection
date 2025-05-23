import cv2
import numpy as np
import os
import sys
import json
import pickle
import logging

# Set up logging
logging.basicConfig(filename='face_recognition.log', level=logging.DEBUG)

def recognize_face(image_path):
    try:
        logging.info(f"Starting face recognition for image: {image_path}")
        
        # Load the image
        image = cv2.imread(image_path)
        if image is None:
            logging.error("Failed to load image")
            return {'success': False, 'message': 'Failed to load image'}

        # Convert to grayscale
        gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
        
        # Apply histogram equalization
        gray = cv2.equalizeHist(gray)

        # Load face detection cascade
        face_cascade = cv2.CascadeClassifier('haarcascade_frontalface_default.xml')
        if face_cascade.empty():
            logging.error("Failed to load face cascade classifier")
            return {'success': False, 'message': 'Failed to load face detection model'}

        # Detect faces
        faces = face_cascade.detectMultiScale(
            gray,
            scaleFactor=1.05,
            minNeighbors=3,
            minSize=(30, 30),
            maxSize=(300, 300)
        )

        if len(faces) == 0:
            logging.warning("No face detected in image")
            return {'success': False, 'message': 'No face detected'}

        # Get the largest face
        largest_face = max(faces, key=lambda rect: rect[2] * rect[3])
        x, y, w, h = largest_face
        logging.info(f"Detected face at position: x={x}, y={y}, w={w}, h={h}")

        # Extract face ROI
        face_roi = gray[y:y+h, x:x+w]
        face_roi = cv2.resize(face_roi, (100, 100))
        
        # Apply Gaussian blur
        face_roi = cv2.GaussianBlur(face_roi, (5, 5), 0)

        # Load LBPH model and labels
        model_path = 'models/lbph_model.yml'
        labels_path = 'models/labels.pkl'

        if not os.path.exists(model_path):
            logging.error("LBPH model file not found")
            return {'success': False, 'message': 'Face recognition model not found'}
            
        if not os.path.exists(labels_path):
            logging.error("Labels file not found")
            return {'success': False, 'message': 'Face recognition labels not found'}

        # Load the model and labels
        try:
            model = cv2.face.LBPHFaceRecognizer_create(
                radius=1,
                neighbors=8,
                grid_x=8,
                grid_y=8,
                threshold=50
            )
            model.read(model_path)
            logging.info("Successfully loaded LBPH model")
            
            with open(labels_path, 'rb') as f:
                labels = pickle.load(f)
            logging.info("Successfully loaded labels")
        except Exception as e:
            logging.error(f"Error loading model or labels: {str(e)}")
            return {'success': False, 'message': 'Error loading face recognition model'}

        # Predict
        try:
            label, confidence = model.predict(face_roi)
            logging.info(f"Prediction result - Label: {label}, Confidence: {confidence}")
            
            # Convert confidence to percentage (LBPH returns lower values for better matches)
            confidence = 100 - confidence
            logging.info(f"Converted confidence: {confidence}%")

            if confidence < 50:  # Threshold for recognition
                logging.warning(f"Confidence too low: {confidence}%")
                return {'success': False, 'message': 'No matching student found'}

            # Get student ID from label
            if label not in labels.values():
                logging.error(f"Label {label} not found in labels dictionary")
                return {'success': False, 'message': 'Invalid recognition result'}

            student_id = [k for k, v in labels.items() if v == label][0]
            logging.info(f"Recognized student ID: {student_id}")

            return {
                'success': True,
                'student_id': student_id,
                'confidence': float(confidence),
                'face_location': {
                    'x': int(x),
                    'y': int(y),
                    'width': int(w),
                    'height': int(h)
                }
            }
        except Exception as e:
            logging.error(f"Error during prediction: {str(e)}")
            return {'success': False, 'message': 'Error during face recognition'}

    except Exception as e:
        logging.error(f"Unexpected error: {str(e)}")
        return {'success': False, 'message': str(e)}

if __name__ == '__main__':
    if len(sys.argv) != 2:
        print(json.dumps({'success': False, 'message': 'Invalid arguments'}))
        sys.exit(1)

    image_path = sys.argv[1]
    result = recognize_face(image_path)
    print(json.dumps(result)) 