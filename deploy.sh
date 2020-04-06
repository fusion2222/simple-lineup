eval "$(ssh-agent -s)"
ssh-add  ~/.ssh/id_rsa
git pull -f origin master
npm install
npm run build
echo "[+] Deployment of simple-lineup plugin finished!"
