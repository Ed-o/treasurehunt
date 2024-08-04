## Treasure Hunt

The Treasure Hunt is a team building event run in your local city.
The idea is that every 5 minutes or so, a message will be sent to each team telling them to collect an item or take a picture near a landmark.
You meet up at the end and the winning team is the one who got the most items/places or best pics or whatever you want.

Credits -
This app uses Mudslide to send the messages to WhatsApp.

Link : https://github.com/robvanderleek/mudslide

This app uses PHP, Apache2, Alpine Linux, Node

To install :
  docker-compose up --build


A note on security - this app is designed to run on a workstation (laptop) that ios running Docker.  
The Database and web port are not secured.  If you want to use it for more than a one of treasure hunt then add some security.


