importPackage(java.io);
importPackage(java.lang);
var reader = new BufferedReader( new InputStreamReader(System['in']) );
while (true){
    var line = reader.readLine();
    if (line==null) break;
    else {
        i = parseInt(line);
        System.out.println((i*i)+'');
        }
    }