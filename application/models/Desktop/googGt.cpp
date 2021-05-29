//************************************************************************
// This googGt.cpp is created to install the google-chrome web browser
// on Ubuntu 14.04 lts 64 bit.
// author@GWade
//************************************************************************

#include <iostream>
#include <fstream>
#include <string>
#include <cstdlib>
#include <locale>

using namespace std;

void PrntGoogPpa(ofstream& googPpa);

void PrntGoogGtr(ofstream& googGtr);

void PrntGoogLst(ofstream& gogLst);

int main()
{

    cout << "Creating the script that adds google-chrome PPA\n" <<endl;

    // create the googPpa.sh shell script
    ofstream googPpa;

    googPpa.open("googPpa.sh");

    PrntGoogPpa(googPpa);

    googPpa.close();

    cout << "Changing the mode of access to executable on the script\n" << endl;
    // change mode of access to executable
    system("chmod +x googPpa.sh");
    cout << "Excuting and installing the Google-Chrome Web Browser\n" << endl;
    system("./googPpa.sh");

    // create an ofstream object and call the function
    cout << "Creating the script that installs google-chrome\n" << endl;
    ofstream googGtr;
    googGtr.open("googGt.sh");
    PrntGoogGtr(googGtr);
    googGtr.close();

    cout << "The googGt.sh script has been created\n" << endl;
    cout << "Changing the mode of access to executable on the script\n" << endl;
    system("chmod +x googGt.sh");
    cout << "Excuting and installing the Google-Chrome Web Browser\n" << endl;
    system("./googGt.sh");

    system("rm -rf /etc/apt/sources.list.d/google-chrome.list");

    ofstream googLst;
    googLst.open("/etc/apt/sources.list.d/google-chrome.list");
    PrntGoogLst(googLst);
    googLst.close();


}
void PrntGoogPpa(ofstream& googPpa)
{

    googPpa << "#! /bin/bash\n\nUPD=\"updatedb\"\n" << endl;

    googPpa << "wget -q -O - "
            << "https://dl-ssl.google.com/linux/linux_signing_key.pub"
            << " | sudo apt-key add -" << "\n" << endl;

    googPpa << "echo \"deb http://dl.google.com/linux/chrome/deb/ stable main\""
            << " >> /etc/apt/sources.list.d/google.list\n\n$UPD\n\nexit" << endl; 

}
void PrntGoogGtr(ofstream& googGtr)
{
    googGtr << "#! /bin/bash\n\nAPGTN=\"apt-get install\"" << endl;

    googGtr << "APUPD=\"apt-get update\"\nUPD=\"updatedb\"\n" << endl;

    googGtr << "$APUPD\n\n$APGTN google-chrome-stable -y\n" << endl;

    googGtr << "$UPD\n\nexit" << endl;

}
void PrntGoogLst(ofstream& googLst)
{

    googLst << "### THIS FILE IS AUTOMATICALLY CONFIGURED ###" << endl;

    googLst << "# You may comment out this entry, but any other modifications"
            << " may be lost." <<endl;

    googLst << "# deb http://dl.google.com/linux/chrome/deb/ stable main" <<endl;

}

