"""Henter de valgte Pexels-billeder i 1800 px (kører i GitHub Actions)."""
import io, os, urllib.request
from PIL import Image
PICK = {
 "romaskine-vand-luft-eller-magnet": 7690207,
 "crosstrainer-eller-lobebaand": 4046658,
 "hjemmegym-paa-et-budget": 6740821,
 "hvor-tunge-haandvaegte-skal-du-koebe": 16513597,
 "power-rack-i-hjemmet": 5837261,
 "massagepistol-saadan-bruger-du-den": 6388978,
 "isbad-derhjemme": 14815629,
 "yogamaatte-tykkelse-materiale-greb": 4793328,
 "pilates-reformer-derhjemme": 25599825,
 "traening-i-lejlighed-stoejsvagt-udstyr": 6598979,
 "kettlebell-hvilken-vaegt-skal-du-vaelge": 3766211,
 "spinningcykel-kondicykel-eller-airbike": 3836831,
 "gaabaand-a": 14546721, "gaabaand-b": 31012865, "gaabaand-c": 8001034,
}
UA = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/124 Safari/537.36"}
os.makedirs("out/articles", exist_ok=True)
for slug, pid in PICK.items():
    url = f"https://images.pexels.com/photos/{pid}/pexels-photo-{pid}.jpeg?auto=compress&cs=tinysrgb&w=1800"
    im = Image.open(io.BytesIO(urllib.request.urlopen(urllib.request.Request(url, headers=UA), timeout=60).read())).convert("RGB")
    im.save(f"out/articles/{slug}.jpg", quality=82, optimize=True, progressive=True)
    print(slug, pid, im.size)
