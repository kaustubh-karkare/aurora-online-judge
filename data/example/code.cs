using System;
class Program {
  static void Main(string[] args){
    int i; string s;
    while ((s = Console.ReadLine()) != null){
      i = Int16.Parse(s);
      Console.WriteLine(i * i);
      }
    }
  }