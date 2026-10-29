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
import re, html as htmlmod
for key, q in TOPICS.items():
    url = "https://unsplash.com/s/photos/" + urllib.parse.quote(q.replace(" ", "-")) + "?orientation=landscape&license=free"
    try:
        page = urllib.request.urlopen(urllib.request.Request(url, headers={**UA, "Accept": "text/html"}), timeout=30).read().decode("utf-8", "replace")
    except Exception as e:
        summary[key] = {"error": str(e)}
        continue
    picks, seen = [], set()
    # Gratis billeder ligger på images.unsplash.com/photo-…; Unsplash+ på plus.unsplash.com (springes over).
    for m in re.finditer(r'<img[^>]+>', page):
        tag = m.group(0)
        src = re.search(r'src="(https://images\.unsplash\.com/photo-[^"?]+)', tag)
        if not src:
            continue
        base = src.group(1)
        if base in seen:
            continue
        seen.add(base)
        alt = re.search(r'alt="([^"]*)"', tag)
        picks.append({"base": base, "alt": htmlmod.unescape(alt.group(1)) if alt else "", "thumb": base + "?w=420&q=70&auto=format&fit=crop&h=260"})
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
