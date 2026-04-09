import onnxruntime as ort
import numpy as np
from PIL import Image
from io import BytesIO
import httpx
from .preprocessor import preprocess_image

LABELS = ["minimal", "partial", "complete"]


class DamageClassifier:
    def __init__(self, model_path: str):
        opts = ort.SessionOptions()
        opts.intra_op_num_threads = 2
        opts.graph_optimization_level = ort.GraphOptimizationLevel.ORT_ENABLE_ALL

        self.session = ort.InferenceSession(
            model_path,
            sess_options=opts,
            providers=["CPUExecutionProvider"],
        )
        self.input_name = self.session.get_inputs()[0].name
        self.is_loaded = True

    def classify_from_url(self, photo_url: str) -> dict:
        with httpx.Client(timeout=10) as client:
            response = client.get(photo_url)
            response.raise_for_status()

        img = Image.open(BytesIO(response.content)).convert("RGB")
        tensor = preprocess_image(img)

        outputs = self.session.run(None, {self.input_name: tensor})
        logits = outputs[0][0]

        exp = np.exp(logits - np.max(logits))
        scores = exp / exp.sum()
        predicted_idx = int(np.argmax(scores))

        return {
            "damage_level": LABELS[predicted_idx],
            "confidence": float(scores[predicted_idx]),
            "scores": {
                "minimal": float(scores[0]),
                "partial": float(scores[1]),
                "complete": float(scores[2]),
            },
        }
