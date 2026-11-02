import sys

fin = sys.argv[1]   
fout = fin.replace("_def", "")

with open(fin, encoding="utf-8") as f:
    cont = f.read()
    cont = cont.replace("\r", "")
    a = cont.split("===")

str = ""
lp = 0
for e in a:
    lp += 1
    str += f"{lp}\n"
    e = e.strip()
    w = e.split("\n")
    ok = -1
    idx = -1
    for x in w:
        if x.startswith("- "):
            idx += 1
            str += f"{x[2:]}\n"
        elif x.startswith("* "):
            idx += 1
            ok = idx
            str += f"{x[2:]}\n"
        else:
            str += f"{x}\n"
    str += chr(ord("a") + ok) + "\n"

with open(fout, "w", encoding="utf-8") as f:
    print(str, file = f)
    