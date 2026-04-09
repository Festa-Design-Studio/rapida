from pydantic import BaseModel, HttpUrl
from typing import Optional


class ClassifyRequest(BaseModel):
    photo_url: HttpUrl
    callback_url: HttpUrl
    job_id: str


class ClassifyResponse(BaseModel):
    status: str
    job_id: str


class ClassifyResult(BaseModel):
    job_id: str
    status: str
    damage_level: Optional[str] = None
    confidence: Optional[float] = None
    scores: Optional[dict] = None
    error: Optional[str] = None
