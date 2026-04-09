import httpx
import os


async def send_callback(callback_url: str, payload: dict):
    secret = os.getenv("INTERNAL_SECRET", "dev-secret")
    async with httpx.AsyncClient(timeout=10) as client:
        await client.post(
            callback_url,
            json=payload,
            headers={"X-Internal-Secret": secret},
        )
