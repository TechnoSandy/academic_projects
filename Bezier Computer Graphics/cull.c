// Satone, Sandeep
// sxs6868
// 2019-11-22 

//----------------------------------------------------------
#include <math.h>
#include <stdio.h>

#include "cull.h"
#include "vertex.h"

//----------------------------------------------------------
int cull( Vertex *v1, Vertex *v2, Vertex *v3, Vertex *cameraPosition )
{
  // TODO: Compute the toTriangle vector.  Compute the triangle
  //       normal vector.  Compute the dot product of these two
  //       vectors.  Return an indication of whether this triangle
  //       should be culled.
  
  //for calculating vectors we first get our points
  double v1x,v1y,v1z,v2x,v2y,v2z,v3x,v3y,v3z;
  //for vector a
  double ax,ay,az;
  //for vector b
  double bx,by,bz;
  //for normal
  //double nx,ny,nz; we can use the array too for keeping nx, ny,nz easier to calculate the dot product using loops
  double n[3];
  //Camera distance 
  double cx, cy,cz;
  //toTriangle vector
  //double toTriangleX, toTriangleY,toTriangleZ;
  double toTriangle[3];
  
  v1x = v1->x;
  v1y = v1->y;
  v1z = v1->z;
  
  v2x = v2->x;
  v2y = v2->y;
  v2z = v2->z;
   
  v3x = v3->x;
  v3y = v3->y;
  v3z = v3->z;
  
  //camera positions 
  cx = cameraPosition->x;
  cy = cameraPosition->y;
  //Camera distance it is in Z axis; if it is 0 using cz as +HUGE_VAL;
  cz = cameraPosition->z;
  
  
  // vector a = v2-v1
  ax = v2x - v1x;
  ay = v2y - v1y;
  az = v2z - v1z;
  
  // vector b = v3-v1
  
  bx = v3x - v1x;
  by = v3y - v1y;
  bz = v3z - v1z;
  
  // vector n ( normal ) = vector a x vector b;
  // Calculate cross product for normal
  
  n[0] = ay*bz - by*az;
  n[1] = bx*az - ax*bz;
  n[2] = ax*by - bx*ay;
  

  // Calculate the toTriangle vector
  toTriangle[0] = v1x - cx;
  toTriangle[1] = v1y - cy;
  toTriangle[2] = v1z - cz;

  
  // Calcute the dot product of toTriangle Vector and Vector n
  double  productVal = 0;
  for (int x = 0 ; x < 3; x ++) {
  	productVal += n[x] * toTriangle[x];
  }

  if(productVal > 0.0)
  	return 1;
  return 0;
  			   
}

//----------------------------------------------------------
