%https://www.mathworks.com/help/imaq/imaqhwinfo.html
a = imaqhwinfo;%Information about available image acquisition hardware
%Get Information about the installed CAM device in the system
[camera_name, camera_id, format] = getCameraInfo(a);


% Capture the video frames using the videoinput function
% You have to replace the resolution & your installed adaptor name.
vid = videoinput(camera_name, camera_id, format);

% Set the properties of the video object
set(vid, 'FramesPerTrigger', Inf);
set(vid, 'ReturnedColorspace', 'rgb')
vid.FrameGrabInterval = 5;

%start the video aquisition here
start(vid)

% Set a loop that stop after 100 frames of aquisition
while(vid.FramesAcquired<=100)
    
    % Get the snapshot of the current frame
    %https://www.mathworks.com/help/imaq/getsnapshot.html
    data = getsnapshot(vid);%immediately returns one single image frame
    
    % Now to track red objects in real time
    % we have to subtract the red component 
    % from the grayscale image to extract the red components in the image.
    diff_im = imsubtract(data(:,:,1), rgb2gray(data));
    %Use a median filter to filter out noise
    diff_im = medfilt2(diff_im, [3 3]);
    % Convert the resulting grayscale image into a binary image.
    %diff_im = im2bw(diff_im,0.18);
    diff_im = im2bw(diff_im,0.10);
    
    % Remove all those pixels less than 300px
    %https://www.mathworks.com/help/images/ref/bwareaopen.html
    diff_im = bwareaopen(diff_im,100);
    
    % Label all the connected components in the image.
    %https://www.mathworks.com/help/images/ref/bwlabel.html
    bw = bwlabel(diff_im, 8);
    
    % Here we do the image blob analysis.
    % We get a set of properties for each labeled region.
    %https://www.mathworks.com/help/images/ref/regionprops.html
    stats = regionprops(bw, 'BoundingBox', 'Centroid');
    
    % Display the image
    %imshow(diff_im)
    %imshow(data)
    subplot(1,2,1)
    %display original image
    imshow(diff_im)
    title('Difference Image')
    subplot(1,2,2)
    %display cropped image
    imshow(data)
    title('Original Image')
    
    %https://www.mathworks.com/help/matlab/ref/hold.html
    hold on
    
    %This is a loop to bound the red objects in a rectangular box.
    for object = 1:length(stats)
        bb = stats(object).BoundingBox;
        bc = stats(object).Centroid;
        rectangle('Position',bb,'EdgeColor','r','LineWidth',2)
        plot(bc(1),bc(2), '-m+')
        %a=text(bc(1)+15,bc(2), strcat('X: ', num2str(round(bc(1))), '    Y: ', num2str(round(bc(2)))));
        %set(a, 'FontName', 'Arial', 'FontWeight', 'bold', 'FontSize', 12, 'Color', 'yellow');
    end
    
    hold off
end
% Both the loops end here.

% Stop the video aquisition.
stop(vid);

% Flush all the image data stored in the memory buffer.
flushdata(vid);

% Clear all variables
clear all
sprintf('%s','That was all about Image tracking, Guess that was pretty easy :) ')

