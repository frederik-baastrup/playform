"""Henter gratis Unsplash-kandidater pr. emne og laver kontaktark til gennemsyn (kører i GitHub Actions)."""
import json, os, urllib.request, urllib.parse, io
from PIL import Image, ImageDraw

TOPICS = {
    "romaskine": "rowing machine",
    "crosstrainer": "elliptical machine",
    "hjemmegym": "home gym",
    "haandvaegte": "dumbbells",
    "powerrack": "squat rack",
    "massagepistol": "massage gun",
    "isbad": "ice bath",
    "yogamaatte": "yoga mat",
    "pilates": "pilates reformer",
    "lejlighed": "home workout living room",
    "gaabaand": "treadmill desk",
    "kettlebell": "kettlebell",
    "spinning": "spin bike",
}
UA = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/124 Safari/537.36", "Accept": "application/json"}
os.makedirs("out", exist_ok=True)
summary = {}
for key, q in TOPICS.items():
    url = "https://unsplash.com/napi/search/photos?" + urllib.parse.urlencode({"query": q, "per_page": 30, "orientation": "landscape"})
    try:
        data = json.load(urllib.request.urlopen(urllib.request.Request(url, headers=UA), timeout=30))
    except Exception as e:
        summary[key] = {"error": str(e)}
        continue
    picks = []
    for r in data.get("results", []):
        if r.get("premium") or r.get("plus") or "plus.unsplash.com" in r["urls"]["raw"]:
            continue
        if r.get("width", 0) < 2400:
            continue
        picks.append({"id": r["id"], "alt": r.get("alt_description") or "", "w": r["width"], "h": r["height"], "user": r["user"]["name"], "raw": r["urls"]["raw"], "thumb": r["urls"]["small"]})
        if len(picks) == 9:
            break
    summary[key] = picks
    # Kontaktark 3x3 med nummer
    sheet = Image.new("RGB", (3 * 420, 3 * 280), "white")
    d = ImageDraw.Draw(sheet)
    for i, p in enumerate(picks):
        try:
            im = Image.open(io.BytesIO(urllib.request.urlopen(urllib.request.Request(p["thumb"], headers=UA), timeout=30).read())).convert("RGB")
        except Exception:
            continue
        im.thumbnail((410, 260))
        x, y = (i % 3) * 420 + 5, (i // 3) * 280 + 5
        sheet.paste(im, (x, y))
        d.rectangle([x, y, x + 34, y + 26], fill="black")
        d.text((x + 8, y + 6), str(i + 1), fill="white")
    sheet.save(f"out/{key}.jpg", quality=80)
json.dump(summary, open("out/candidates.json", "w"), indent=1, ensure_ascii=False)
print({k: (len(v) if isinstance(v, list) else v) for k, v in summary.items()})
