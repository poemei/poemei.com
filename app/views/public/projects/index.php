<?php require APPROOT . '/views/inc/head.php'; ?>
<div class="container">
  <div class="row">
    <h1>Projects</h1>
    <p>Just a short list of my current projects.</p>
    <?php
    $projects = "
    **This MVC**
    An MVC is a web based platform that has been around for a very long time.
      - The **M**odel is what deals with Database Functionality
      - The **V**iew presents the data from the **C**controller
      - The **C**controller is the `Traffic Cop`, directing traffic while injecting data.
    I chose to develop a custom version of this platform, that acts like a **Content Management System**.
    
    **The Chaos CMS**
    Chaos is very much a part of my life, so the naming became very important and the development was chaotic.
      - Is to be shelved and converted to this **MVC**
      - On the web at [Chaos CMS](https://www.chaoscms.org)
     
     **Stn-Labz**
     My Cyber Security Project
      - Utilizing the `Sentinel` systems to gather actionable intelligence that can can be used in `machine learning` to protect the world from these bad actors and bots.
      - The space just before where your web site loads once it has been called in a browser, is the `Red Zone` and that `Red Zone` must be protected, hence why the ** Sentinel Threat Network** was born.
      - On the Web at [STN-Labz](https://www.stn-labz.com)
      - I am the **Owner/Operator** of this organization.
      
      **My Expansive Tradition**
       - Not based on **Wicca** or any other other known religion.
       - Severely follow the `KISS` theory (**Keep it Stupid Simple**).
       - Is based on the Sovereign self, not known deities of `Pagan` traditions.
       - On the Web at [Ars Rosaic](https://www.arsrosaic.org)
       
       **The Indicia Institue**
       A part of the **Rosaic Traditon**.
        - To provide academic instruction for certain grades and positions within the `tradition`.
        - On the Web at [Indicia Institute](https://www.arsrosaic.org/s/indicia).
        - I am the Chief architect and dean of this institute.
    ";
    echo $this->render_md->markdown($projects);
    ?>
    <small><em>This page is MarkDown, rendered by my custom Markdown Rendering Engine</em></small>
  </div>
</div>
<?php require APPROOT . '/views/inc/foot.php'; ?>
