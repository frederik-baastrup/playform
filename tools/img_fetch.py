"""Henter Pexels-billeder (slug id pr. linje) i 1800 px og laver et kontaktark (kører i GitHub Actions)."""
import io, os, urllib.request
from PIL import Image, ImageDraw
UA = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/124 Safari/537.36"}
os.makedirs("out/articles", exist_ok=True)
tiles = []
for line in open("tools/img_list.txt"):
    if not line.strip(): continue
    slug, pid = line.split()
    url = f"https://images.pexels.com/photos/{pid}/pexels-photo-{pid}.jpeg?auto=compress&cs=tinysrgb&w=1800"
    try:
        im = Image.open(io.BytesIO(urllib.request.urlopen(urllib.request.Request(url, headers=UA), timeout=60).read())).convert("RGB")
    except Exception as e:
        print(slug, pid, "FEJL", e); continue
    im.save(f"out/articles/{slug}.jpg", quality=82, optimize=True, progressive=True)
    t = im.copy(); t.thumbnail((480, 320)); tiles.append((f"{slug} {pid} {im.size[0]}x{im.size[1]}", t))
cols = 3; rows = (len(tiles) + 2) // 3
sheet = Image.new("RGB", (cols * 490, max(1, rows) * 350), "white"); d = ImageDraw.Draw(sheet)
for i, (lbl, t) in enumerate(tiles):
    x, y = (i % 3) * 490 + 5, (i // 3) * 350 + 5
    sheet.paste(t, (x, y)); d.rectangle([x, y + 320, x + 480, y + 342], fill="black"); d.text((x + 6, y + 326), lbl, fill="white")
sheet.save("out/sheet.jpg", quality=82)
