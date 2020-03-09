import java.io.DataInput;
import java.io.DataOutput;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Scanner;

import org.apache.hadoop.conf.Configured;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.LongWritable;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.io.Writable;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.Mapper;
import org.apache.hadoop.mapreduce.Reducer;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.input.SequenceFileInputFormat;
import org.apache.hadoop.mapreduce.lib.input.TextInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import org.apache.hadoop.mapreduce.lib.output.SequenceFileOutputFormat;
import org.apache.hadoop.mapreduce.lib.output.TextOutputFormat;

// class Vertex extends Writable {
//	  short tag;                 // 0 for a graph vertex, 1 for a group number
//	  long group;                // the group where this vertex belongs to
//	  long VID;                  // the vertex ID
//	  long[] adjacent;           // the vertex neighbors
//	  ...
//	}
// Vertex must have two constructors: Vertex(tag,group,VID,adjacent) and Vertex(tag,group).

class Vertex implements Writable {
	short tag;
	long group;
	long VID;
	long size;
	// We have taken ArrayList instead of Array because it is better
	// if you want to add new element instead of creating new array everytime we
	// prefer arraylist
	ArrayList<Long> adjacent;

	public Vertex() {
		super();
	}

	Vertex(short tag, long group) {
		this.tag = tag;
		this.group = group;
	}

	Vertex(short tag, long group, long VID, ArrayList<Long> adjacent, long size) {
		this.tag = tag;
		this.group = group;
		this.VID = VID;
		this.adjacent = new ArrayList<Long>(adjacent);
		this.size = size;
	}

	@Override
	public void write(DataOutput out) throws IOException {
		out.writeShort(tag);
		out.writeLong(group);
		out.writeLong(VID);
		out.writeLong(size);
		for (int i = 0; i < size; i++) {
			out.writeLong(adjacent.get(i));
		}
	}

	@Override
	public void readFields(DataInput in) throws IOException {
		// TODO Auto-generated method stub
		tag = in.readShort();
		group = in.readLong();
		VID = in.readLong();
		size = in.readLong();
		adjacent = new ArrayList<Long>((int) size);
		for (int i = 0; i < size; i++) {
			adjacent.add(in.readLong());
		}

	}

	@Override
	public String toString() {
		return tag + " " + group + " " + VID + " " + adjacent;
	}

}

public class Graph extends Configured {

//	First Map-Reduce job:
//	-------------------------------------------------------------------------
//
//		map ( key, line ) =
//		  parse the line to get the vertex VID and the adjacent vector
//		  emit( VID, new Vertex(0,VID,VID,adjacent) )

	public static class Mapper1 extends Mapper<Object, Text, LongWritable, Vertex> {
		private Scanner scanner;

		@Override
		public void map(Object key, Text value, Context context) throws IOException, InterruptedException {
			scanner = new Scanner(value.toString());
			Scanner Scan = scanner.useDelimiter(",");
			long vid = Scan.nextLong();
			ArrayList<Long> adjacent = new ArrayList<>();
			while (Scan.hasNextLong()) {
				adjacent.add(Scan.nextLong());
			}
			context.write(new LongWritable(vid), new Vertex((short) 0, vid, vid, adjacent, adjacent.size()));
			Scan.close();
		}

	}

//	Second Map-Reduce job:
//		--------------------------------------------------------------------------------------------
//		map ( key, vertex ) =
//		  emit( vertex.VID, vertex )   // pass the graph topology
//		  for n in vertex.adjacent:
//		     emit( n, new Vertex(1,vertex.group) )  // send the group # to the adjacent vertices

	public static class Mapper2 extends Mapper<Object, Vertex, LongWritable, Vertex> {
		@Override
		public void map(Object key, Vertex vertex, Context context) throws IOException, InterruptedException {
			context.write(new LongWritable(vertex.VID), vertex);
			for (long n : vertex.adjacent) {
				context.write(new LongWritable(n), new Vertex((short) 1, vertex.group));
			}
		}
	}
//	Second Map-Reduce job:
//	--------------------------------------------------------------------------------------------
//	reduce ( vid, values ) =
//	  m = Long.MAX_VALUE;
//	  for v in values:
//	     if v.tag == 0
//	        then adj = v.adjacent.clone()     // found the vertex with vid
//	     m = min(m,v.group)
//	  emit( m, new Vertex(0,m,vid,adj) )      // new group #

	public static class Reducer2 extends Reducer<LongWritable, Vertex, LongWritable, Vertex> {
		@Override
		public void reduce(LongWritable key, Iterable<Vertex> values, Context context)
				throws IOException, InterruptedException {
			long m = Long.MAX_VALUE;
			long VID = key.get();
			Vertex vertex = null;
			for (Vertex v : values) {
				if (v.tag == 0) {
					vertex = new Vertex(v.tag, v.group, v.VID, v.adjacent, v.size);
				}
				m = Math.min(m, v.group);
			}
			context.write(new LongWritable(m), new Vertex((short) 0, m, VID, vertex.adjacent, vertex.size));

		}
	}

//	Final Map-Reduce job:
//	-----------------------------------------
//		map ( group, value ) =
//		   emit(group,1)

	public static class Mapper3 extends Mapper<LongWritable, Vertex, LongWritable, LongWritable> {
		@Override
		public void map(LongWritable key, Vertex value, Context context) throws IOException, InterruptedException {
			LongWritable L = new LongWritable(1);
			context.write(key, L);
		}

	}
//	Final Map-Reduce job:
//	-----------------------------------------
//	reduce ( group, values ) =
//	   m = 0
//	   for v in values
//	       m = m+v
//	   emit(group,m)

	public static class Reducer3 extends Reducer<LongWritable, LongWritable, LongWritable, LongWritable> {

		@Override
		public void reduce(LongWritable key, Iterable<LongWritable> values, Context context)
				throws IOException, InterruptedException {
			long m = 0;
			for (LongWritable v : values) {
				m = m + v.get();
			}
			context.write(key, new LongWritable(m));
		}
	}

	public static void main(String[] args) throws Exception {
		Job job = Job.getInstance();
		job.setJobName("job1");
		job.setJarByClass(Graph.class);
		job.setOutputKeyClass(LongWritable.class);
		job.setOutputValueClass(Vertex.class);
		job.setMapOutputKeyClass(LongWritable.class);
		job.setMapOutputValueClass(Vertex.class);
		job.setMapperClass(Mapper1.class);
		job.setInputFormatClass(TextInputFormat.class);
		job.setOutputFormatClass(SequenceFileOutputFormat.class);
		FileInputFormat.setInputPaths(job, new Path(args[0]));
		Path intermediate_Directory = new Path(args[1] + "/f0");
		FileOutputFormat.setOutputPath(job, intermediate_Directory);
		job.waitForCompletion(true);

		for (int i = 0; i < 5; i++) {
			Job job2 = Job.getInstance();
			job2.setJobName("job2");
			job2.setJarByClass(Graph.class);
			job2.setOutputKeyClass(LongWritable.class);
			job2.setOutputValueClass(Vertex.class);
			job2.setMapOutputKeyClass(LongWritable.class);
			job2.setMapOutputValueClass(Vertex.class);
			job2.setMapperClass(Mapper2.class);
			job2.setReducerClass(Reducer2.class);
			job2.setInputFormatClass(SequenceFileInputFormat.class);
			job2.setOutputFormatClass(SequenceFileOutputFormat.class);
			intermediate_Directory = new Path(args[1] + "/f" + i);
			FileInputFormat.setInputPaths(job2, intermediate_Directory);
			intermediate_Directory = new Path(args[1] + "/f" + (i + 1));
			FileOutputFormat.setOutputPath(job2, intermediate_Directory);
			job2.waitForCompletion(true);
		}

		Job job3 = Job.getInstance();
		job3.setJobName("job3");
		job3.setJarByClass(Graph.class);
		job3.setOutputKeyClass(LongWritable.class);
		job3.setOutputValueClass(LongWritable.class);
		job3.setMapOutputKeyClass(LongWritable.class);
		job3.setMapOutputValueClass(LongWritable.class);
		job3.setMapperClass(Mapper3.class);
		job3.setReducerClass(Reducer3.class);
		job3.setInputFormatClass(SequenceFileInputFormat.class);
		job3.setOutputFormatClass(TextOutputFormat.class);
		intermediate_Directory = new Path(args[1] + "/f5");
		FileInputFormat.setInputPaths(job3, intermediate_Directory);
		FileOutputFormat.setOutputPath(job3, new Path(args[2]));
		job3.waitForCompletion(true);
	}

}
