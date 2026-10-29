"""Henter Pexels-kandidater (efter ID) og laver kontaktark med ID og opløsning (kører i GitHub Actions)."""
import io, json, os, urllib.request
from PIL import Image, ImageDraw

C = {
 "romaskine": [38453227, 30021732, 7690207, 31818709, 29526373, 6551066, 6389867, 6551083],
 "crosstrainer": [47084, 3757954, 3931064, 3930999, 8933584, 4046658],
 "hjemmegym": [7174396, 34213999, 4397831, 6339719, 11513151, 6740821, 17227607, 6389892],
 "haandvaegte": [2652236, 7209145, 4162451, 16513597, 260352, 949131, 4397833, 1229356],
 "powerrack": [1552106, 5327530, 2628215, 27810163, 5837261, 14623740, 4164450],
 "massagepistol": [11372618, 11372642, 6389069, 11372613, 6388978, 5327481],
 "isbad": [14815624, 14815629, 11931271, 7041618, 4155442],
 "yogamaatte": [3823203, 4793328, 8539032, 4534632, 8436750, 6634977, 2836961],
 "pilates": [25599821, 31509828, 25599825, 25596671, 11075077],
 "lejlighed": [6598979, 6443479, 6246251, 4148919, 8846511, 8121238],
 "gaabaand": [3757957, 4944978, 4944982, 1954524, 12250460, 30704307],
 "kettlebell": [221247, 14502821, 13993022, 7322458, 3766211, 2836961],
 "spinning": [7689278, 19254708, 775025, 3836831],
}
UA = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/124 Safari/537.36"}
os.makedirs("out", exist_ok=True)
info = {}
for key, ids in C.items():
    tiles = []
    for pid in ids:
        url = f"https://images.pexels.com/photos/{pid}/pexels-photo-{pid}.jpeg?auto=compress&cs=tinysrgb&w=2400"
        try:
            im = Image.open(io.BytesIO(urllib.request.urlopen(urllib.request.Request(url, headers=UA), timeout=40).read())).convert("RGB")
        except Exception as e:
            info[pid] = str(e)
            continue
        info[pid] = im.size
        t = im.copy(); t.thumbnail((480, 320))
        tiles.append((pid, im.size, t))
    cols = 3
    rows = (len(tiles) + cols - 1) // cols
    sheet = Image.new("RGB", (cols * 490, rows * 350), "white")
    d = ImageDraw.Draw(sheet)
    for i, (pid, size, t) in enumerate(tiles):
        x, y = (i % cols) * 490 + 5, (i // cols) * 350 + 5
        sheet.paste(t, (x, y))
        d.rectangle([x, y + 320, x + 480, y + 342], fill="black")
        d.text((x + 6, y + 326), f"{pid}  {size[0]}x{size[1]}", fill="white")
    sheet.save(f"out/{key}.jpg", quality=82)
json.dump(info, open("out/info.json", "w"), indent=1)
