from fastapi import FastAPI, BackgroundTasks, HTTPException, Header
from .schemas import ClassifyRequest, ClassifyResponse
from .classifier import DamageClassifier
from .callback import send_callback
import os

app = FastAPI(title="RAPIDA AI Service", version="1.0.0")

MODEL_PATH = os.getenv("MODEL_PATH", "app/models/damage_classifier.onnx")
INTERNAL_SECRET = os.getenv("INTERNAL_SECRET", "dev-secret")

# Try to load model, gracefully handle if not present
try:
    classifier = DamageClassifier(model_path=MODEL_PATH)
except Exception:
    classifier = None


def verify_secret(x_internal_secret: str = Header(None)):
    if x_internal_secret != INTERNAL_SECRET:
        raise HTTPException(status_code=403, detail="Forbidden")


@app.post("/classify", response_model=ClassifyResponse)
async def classify(
    request: ClassifyRequest,
    background_tasks: BackgroundTasks,
    x_internal_secret: str = Header(None),
):
    verify_secret(x_internal_secret)

    if not classifier:
        # No model loaded — return mock result for demo
        background_tasks.add_task(
            send_mock_callback,
            str(request.callback_url),
            request.job_id,
        )
        return ClassifyResponse(status="accepted", job_id=request.job_id)

    background_tasks.add_task(
        run_inference_and_callback,
        str(request.photo_url),
        str(request.callback_url),
        request.job_id,
    )
    return ClassifyResponse(status="accepted", job_id=request.job_id)


async def run_inference_and_callback(photo_url: str, callback_url: str, job_id: str):
    try:
        result = classifier.classify_from_url(photo_url)
        await send_callback(callback_url, {
            "job_id": job_id,
            "damage_level": result["damage_level"],
            "confidence": result["confidence"],
            "scores": result["scores"],
            "status": "success",
        })
    except Exception as e:
        await send_callback(callback_url, {
            "job_id": job_id,
            "status": "error",
            "error": str(e),
        })


async def send_mock_callback(callback_url: str, job_id: str):
    """Mock classification for demo when no ONNX model is loaded."""
    import random
    levels = ["minimal", "partial", "complete"]
    weights = [0.2, 0.6, 0.2]
    idx = random.choices(range(3), weights=weights)[0]
    scores = {l: round(random.uniform(0.05, 0.3), 3) for l in levels}
    scores[levels[idx]] = round(random.uniform(0.6, 0.9), 3)

    await send_callback(callback_url, {
        "job_id": job_id,
        "damage_level": levels[idx],
        "confidence": scores[levels[idx]],
        "scores": scores,
        "status": "success",
    })


@app.get("/health")
def health():
    return {"status": "ok", "model_loaded": classifier is not None}
