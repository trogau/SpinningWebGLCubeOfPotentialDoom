# SpinningWebGLCubeOfPotentialDoom
WebGL implementation of the Spinning Cube of Potential Doom

The Spinning Cube of Potential Doom (https://www.nersc.gov/news-publications/nersc-news/nersc-center-news/1998/cube-of-doom/) was first created by Steve Lau, staff member at the National Energy Research Scientific Computing Center (NERSC). It took data from an intrusion detection system called 'Bro' and visualised it in a 3D cube. 

It has been stuck in my brain for many years; I even emailed the author about it to see if it might get open sourced or released, because I loved the idea of it. 

I realised recently that building something like this would not actually be that hard so I spent a few hours hacking something together using WebGL and tcpdump. 

The connecting IP is mapped on the x-axis. Ports are on the y-axis, so on a webserver you'll tend to see lots of activity towards the bottom of the cube (ports 80 and 443). z-axis should be for the local IP but it's currently fixed at zero. 

[![IMAGE ALT TEXT](https://trog.qgl.org/up/2004/cube.jpg)](http://www.youtube.com/watch?v=QtRwG7hYLhU "Video Title")

## Instructions

1) Check out this repository into a web-facing directory on a PHP-enabled webserver
2) Add the target IP address of your system into stream.php
3) Put the public-facing URL to stream.php in index.html in the 'new EventSource' line. 
4) Start up a tcpdump, outputting to the file livedump.txt - e.g., I use: 
 * tcpdump -l -n -i eth0 -s 1500 port not 22 and not icmp and dst x.x.x.x > livedump.txt
5) Open index.html in a web browser and look for activity. 
  
## TODO

* Replace getting input by tailing a tcpdump output file with a message queue to make it more robust & scalable.
* Make it so z-axis corresponds to local IP address so it can be run on a network instead of just a single system. 
* Figure out why additive blending isn't working to better show hotspots of activity. 
* Properly use three.js instead of just hackishly downloading a few files.   

