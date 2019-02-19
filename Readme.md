
# Bloomon Facility Simulation
This project is simulating Bloomon flowers - bouquets matching. 

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites
 - Docker
 
 ### Installing & Running
 
 1. Clone the project to your machine
 
     ```
     git clone https://github.com/ayoubabozer/bloomon.git
     ```
 
 2. Get into the Dir 
     ```
     cd bloomon
     ```
    
 3. Build Docker
 
    ```
     docker build -t <tag_name> .
     ```
    
     - don't forget the dot.
 
 4. Running
 
    Simulating from standard input
      ```
         docker run --rm -it <tag_name> php startFromInputs.php
      ```
 
    Simulating from `input.txt`
     ```
        docker run --rm -it <tag_name> php startFromFile.php
     ```
     - --rm : This tells Docker to “remove” the container after the command is run.
     - -it : Make sure that it runs in interactive mode with the terminal attached.
     

# ENJOY!.
