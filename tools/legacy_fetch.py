"""Henter alle sider fra det gamle fitbase.dk via sitemap (kører i GitHub Actions)."""
import json, os, re, urllib.request, xml.etree.ElementTree as ET
from bs4 import BeautifulSoup
UA = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 Chrome/124 Safari/537.36"}
BASE = "https://fitbase.dk"
def get(url):
    return urllib.request.urlopen(urllib.request.Request(url, headers=UA), timeout=40).read()
os.makedirs("out/html", exist_ok=True)
log = []
sitemaps, urls = [], []
for s in ["/sitemap_index.xml", "/wp-sitemap.xml", "/sitemap.xml"]:
    try:
        get(BASE + s); sitemaps.append(BASE + s); break
    except Exception as e:
        log.append(f"{s}: {e}")
seen = set()
while sitemaps:
    sm = sitemaps.pop()
    if sm in seen: continue
    seen.add(sm)
    try:
        root = ET.fromstring(get(sm))
    except Exception as e:
        log.append(f"{sm}: {e}"); continue
    ns = {"s": "http://www.sitemaps.org/schemas/sitemap/0.9"}
    for loc in root.findall(".//s:sitemap/s:loc", ns): sitemaps.append(loc.text.strip())
    for u in root.findall(".//s:url", ns):
        loc = u.find("s:loc", ns).text.strip()
        lm = u.find("s:lastmod", ns)
        urls.append({"url": loc, "sitemap": sm, "lastmod": lm.text if lm is not None else ""})
log.append(f"sitemaps: {sorted(seen)}")
pages = []
for i, u in enumerate(urls):
    try:
        html = get(u["url"]).decode("utf-8", "replace")
    except Exception as e:
        log.append(f"{u['url']}: {e}"); continue
    slug = u["url"].replace(BASE, "").strip("/").replace("/", "__") or "forside"
    open(f"out/html/{slug}.html", "w").write(html)
    soup = BeautifulSoup(html, "html.parser")
    meta = lambda **k: (soup.find("meta", attrs=k) or {}).get("content", "")
    body = soup.select_one(".entry-content") or soup.select_one("article") or soup.select_one("main")
    text = body.get_text("\n", strip=True) if body else ""
    pages.append({
        **u,
        "path": "/" + u["url"].replace(BASE, "").strip("/") + "/",
        "title": soup.title.get_text(strip=True) if soup.title else "",
        "h1": soup.h1.get_text(strip=True) if soup.h1 else "",
        "description": meta(name="description") or meta(property="og:description"),
        "published": meta(property="article:published_time"),
        "modified": meta(property="article:modified_time"),
        "type": "post" if soup.select_one("body.single-post, body.single") else ("page" if soup.select_one("body.page") else "other"),
        "words": len(text.split()),
        "h2": [h.get_text(" ", strip=True) for h in (body.find_all(["h2", "h3"]) if body else [])],
        "outlinks": sorted({a["href"] for a in (body.find_all("a", href=True) if body else []) if "fitbase.dk" not in a["href"] and a["href"].startswith("http")})[:40],
        "text": text,
    })
json.dump(pages, open("out/pages.json", "w"), ensure_ascii=False, indent=1)
open("out/log.txt", "w").write("\n".join(log))
print(len(urls), "urls,", len(pages), "pages")
