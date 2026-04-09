import numpy as np
from PIL import Image

IMAGE_SIZE = 300
MEAN = np.array([0.485, 0.456, 0.406], dtype=np.float32)
STD = np.array([0.229, 0.224, 0.225], dtype=np.float32)


def preprocess_image(img: Image.Image) -> np.ndarray:
    img = img.resize((IMAGE_SIZE, IMAGE_SIZE), Image.BICUBIC)
    arr = np.array(img, dtype=np.float32) / 255.0
    arr = (arr - MEAN) / STD
    arr = arr.transpose(2, 0, 1)
    arr = np.expand_dims(arr, axis=0)
    return arr.astype(np.float32)
