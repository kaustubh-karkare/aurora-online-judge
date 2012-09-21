import java.io.*;
public class code {
	public static void main(String args[])throws IOException{
		BufferedReader in = new BufferedReader(new InputStreamReader(System.in));
		int n;
		String str;
		while((str=in.readLine())!=null){
			n = Integer.parseInt(str);
			n = n*n;
			System.out.println(n);
			} // while
		}
	}