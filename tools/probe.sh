UA="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36"
mkdir -p out
for u in \
 "https://www.pexels.com/search/rowing%20machine/" \
 "https://www.pexels.com/en-us/api/v3/search/photos?query=rowing%20machine&per_page=24" \
 "https://images.pexels.com/photos/6551083/pexels-photo-6551083.jpeg?auto=compress&w=1600" \
 "https://unsplash.com/photos/0xn-8kRWOhE/download?force=true&w=1600" \
 "https://unsplash.com/photos/0xn-8kRWOhE" \
 "https://unsplash.com/s/photos/rowing-machine" ; do
  code=$(curl -s -L -A "$UA" -H "Accept-Language: en" -o out/body -w "%{http_code} %{content_type} %{size_download} %{url_effective}" "$u")
  echo "$u -> $code" | tee -a out/probe.txt
done
